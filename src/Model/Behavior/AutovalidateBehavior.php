<?php
/**
 * Source code for the Database.AutovalidateBehavior class.
 *
 */
namespace Database\Model\Behavior;

use Cake\Cache\Cache;
use Cake\ORM\Behavior;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * The class AutoValidateBehavior from the Database plugin automatically adds
 * the minimal rules ensuring no database exception.
 *
 * INFO: put in a Database plugin ?
 *
 * Rules are added from field definitions, unique indexes and foreign keys.
 */
class AutovalidateBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * Accepted keys:
     *  - accepted: validation rules will only be added if the name of the validator
     *    is in this array. Default is NULL which means any name.
     *  - cache: wether or not to use the cache
     *  - domain: the domain name for translations
     *
     * @var array
     */
    protected $_defaultConfig = [
        'accepted' => null,
        'cache' => null,
        'domain' => 'database'
    ];

    /**
     * Are the rules already loaded ?
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * Represents validation rules that can be added to the validator.
     */
    const ADD = 'add';

    /**
     * Represents field names that can be empty.
     */
    const ALLOW_EMPTY = 'allowEmpty';

    /**
     * Represents arrays with column, table keys as foreign key constraints.
     */
    const EXISTS_IN = 'existsIn';

    /**
     * Represents arrays of columns as unique constraints.
     */
    const IS_UNIQUE = 'isUnique';

    /**
     * Represents field names that cannot be empty.
     */
    const NOT_EMPTY = 'notEmpty';

    /**
     * Represents field names that cannot be omitted.
     */
    const REQUIRE_PRESENCE = 'requirePresence';

    /**
     * Live cache where rules will be loaded.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Cache key name.
     *
     * @see class::cacheKey()
     * @var array
     */
    protected $cacheKey = null;

    /**
     * Map from column types to validation rules.
     *
     * @see http://book.cakephp.org/3.0/en/development/testing.html#fixtures
     *
     * @var array
     */
    protected $map = [
        'biginteger' => 'isInteger',
        'boolean' => 'boolean',
        'date' => 'date',
        'datetime' => 'datetime',
        'decimal' => 'numeric',
        'float' => 'numeric',
        'integer' => 'isInteger',
        'numeric' => 'numeric',
        'time' => 'time',
        'timestamp' => 'datetime',
        'uuid' => 'uuid'
    ];

    /**
     * Return the cache key name for the current table, language and domain.
     *
     * @return string
     */
    protected function cacheKey()
    {
        if ($this->cacheKey === null) {
            $plugin = Inflector::underscore(\Database\namespaceRoot(__CLASS__));
            $class = Inflector::underscore(\Database\namespaceTail(__CLASS__));
            $connection = Inflector::underscore($this->_table->connection()->configName());
            $table = Inflector::underscore($this->_table->table());
            $lang = strtolower(ini_get('intl.default_locale'));
            $domain = $this->config('domain');
            $this->cacheKey = $plugin . '_' . $class . '_' . $connection . '_' . $table . '_' . $lang . '_' . $domain;
        }

        return $this->cacheKey;
    }

    /**
     * Returns extracted unique indexes columns (isUnique) and foreign key
     * constraints(existsIn)from the table.
     *
     * @return array
     */
    protected function extractConstraints()
    {
        $result = [];

        foreach ($this->_table->schema()->constraints() as $name) {
            $constraint = $this->_table->schema()->constraint($name);
            if ($constraint['type'] === 'unique') {
                $result[self::IS_UNIQUE][] = $constraint['columns'];
                if (count($constraint['columns']) === 1) {
                    $result[self::ADD][] = [
                        $constraint['columns'][0],
                        'unique',
                        [
                            'rule' => ['validateUnique'],
                            'provider' => 'table',
                            'message' => __d('database', 'Validate::isUnique')
                        ]
                    ];
                }
            } elseif ($constraint['type'] === 'foreign') {
                $result[self::EXISTS_IN][] = [
                    'columns' => $constraint['columns'],
                    //INFO: ne passe pas dans le test unitaire avec parent_id
                    'table' => Inflector::camelize($constraint['references'][0])
                ];
            }
        }

        return $result;
    }

    /**
     * Returns extracted Validator rules (add), a list of fields that are required
     * (requirePresence), a list of fields that cannot be empty (notEmpty) and
     * a list of fields that can be left empty (allowEmpty).
     *
     * Field definition rules are computed as follows:
     *  - auto increment fields are never required
     *  - columns with a default of NULL are required on create if they cannot be NULL
     *  - columns with a default other than NULL are never required
     *  - columns that cannot be NULL cannot be empty
     *  - columns that can be NULL can be empty
     *  - other rules are added by column type
     *  - if a column is a string with a length, a maxLength rule will be added
     *
     * @return array
     */
    protected function extractColumnDefinitions()
    {
        $result = [];

        foreach ($this->_table->schema()->columns() as $name) {
            $infos = $this->_table->schema()->column($name);
            if (Hash::get($infos, 'autoIncrement') !== true) {
                if ($infos['null'] === false) {
                    $result[self::NOT_EMPTY][] = $name;

                    //INFO: CakePHP 3.1beta2 / PostgreSQL 8.4: 'NULL::bpchar'
                    if (in_array($infos['default'], [null, '']) || preg_match('/^NULL::/', $infos['default'])) {
                        $result[self::REQUIRE_PRESENCE][] = $name;
                    }
                } elseif ($infos['default'] === null) {
                    $result[self::ALLOW_EMPTY][] = $name;
                }
            }

            if ($infos['type'] === 'string') {
                $result[self::ADD][] = [
                    $name,
                    'maxLength',
                    [
                        'rule' => ['maxLength', $infos['length']],
                        'message' => sprintf(__d($this->config('domain'), 'Validate::maxLength'), $infos['length'])
                    ]
                ];
            }

            if (isset($this->map[$infos['type']])) {
                $result[self::ADD][] = [
                    $name,
                    $this->map[$infos['type']],
                    [
                        'rule' => [$this->map[$infos['type']]],
                        'message' => sprintf(__d($this->config('domain'), "Validate::{$this->map[$infos['type']]}"))
                    ]
                ];
            }
        }

        return $result;
    }

    /**
     * Returns one of the following, live and normal caching:
     *  - add: rules to add
     *  - allowEmpty: list of fields that can be left empty
     *  - existsIn: array with a foreign key array and the corresponding table
     *  - isUnique: unique column groups
     *  - notEmpty: list of fields that cannot be empty
     *  - requirePresence: list of required fields
     *
     * @see AutovalidateBehavior::config('cache')
     * @see AutovalidateBehavior::cacheKey()
     * @see AutovalidateBehavior::extractColumnDefinitions()
     * @see AutovalidateBehavior::extractConstraints()
     *
     * @param string $type The type of rules to return.
     * @return array
     */
    protected function getCached($type)
    {
        if ($this->loaded === false) {
            $cacheKey = $this->cacheKey();

            $cache = Cache::read($cacheKey);

            if ($this->config('cache') === false || $cache === false) {
                $cache = $this->extractColumnDefinitions();
                foreach ($this->extractConstraints() as $key => $value) {
                    $cache[$key] = array_merge((array)Hash::get($cache, $key), $value);
                }

                Cache::write($cacheKey, $cache);
            }

            $this->cache = $cache;
            $this->loaded = true;
        }

        return (array)Hash::get($this->cache, $type);
    }

    /**
     * Automatically add the translated validation rules if the event is not stopped
     * and if the name of the validator is amongst the accepted ones.
     *
     * @source http://book.cakephp.org/3.0/en/core-libraries/validation.html#validating-data
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Cake\Event\Event $event The calling event
     * @param \Cake\Validation\Validator $validator The validator object
     * @param string $name The name of the validator oject
     * @return void
     */
    public function buildValidator(\Cake\Event\Event $event, \Cake\Validation\Validator $validator, $name)
    {
        $accepted = $this->config('accepted');

        if ($accepted === null || in_array($name, (array)$accepted)) {
            foreach ($this->getCached(self::ADD) as $add) {
                call_user_func_array([$validator, 'add'], $add);
            }

            $requirePresenceMsg = __d($this->config('domain'), 'Validate::requirePresence');
            foreach ($this->getCached(self::REQUIRE_PRESENCE) as $requirePresence) {
                $validator->requirePresence($requirePresence, 'create', $requirePresenceMsg);
            }

            $notEmptyMessage = __d($this->config('domain'), 'Validate::notEmpty');
            foreach ($this->getCached(self::NOT_EMPTY) as $notEmpty) {
                $validator->notEmpty($notEmpty, $notEmptyMessage);
            }

            foreach ($this->getCached(self::ALLOW_EMPTY) as $allowEmpty) {
                $validator->allowEmpty($allowEmpty);
            }
        }
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Cake\Event\Event $event The calling event
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(\Cake\Event\Event $event, RulesChecker $rules)
    {
        // Unique indexes
        $isUniqueMsg = __d($this->config('domain'), 'Validate::isUnique');
        foreach ($this->getCached(self::IS_UNIQUE) as $columns) {
            $rules->add($rules->isUnique($columns, $isUniqueMsg));
        }

        // Foreign keys
        $existsInMsg = __d($this->config('domain'), 'Validate::existsIn');
        foreach ($this->getCached(self::EXISTS_IN) as $existsIn) {
            $rules->add($rules->existsIn($existsIn['columns'], TableRegistry::get($existsIn['table']), $existsInMsg));
        }

        return $rules;
    }
}
