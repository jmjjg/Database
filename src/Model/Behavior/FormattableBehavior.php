<?php
/**
 * Source code for the Database.FormattableBehavior class.
 *
 */
namespace Database\Model\Behavior;

use ArrayObject;
use Cake\Cache\Cache;
use Cake\ORM\Behavior;
use Cake\Utility\Inflector;
use Database\Utility\CodeLogic;

/**
 * This behavior provides a mechanism for applying formatters to field values
 * before validation or saving.
 *
 * Config keys:
 *  - cache: wether or not to use the cache; boolean default NULL is equivalent to true
 *  - formatters: a list of formatters that need to be functions or static
 *    methods, with their fully qualified namespace as keys.
 *
 * Accepted formatters values are boolean true and false, field name as a regular expression
 * string, field type as a string, array of strings, an array with a NOT key whose
 * value can be a string or an array.
 */
class FormattableBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'cache' => null,
        'formatters' => [
            '\\Database\\Utility\\Formatter::formatSuffix' => '/_id$/',
            '\\Database\\Utility\\Formatter::formatTrim' => [ 'NOT' => 'binary'],
            '\\Database\\Utility\\Formatter::formatNull' => true,
            '\\Database\\Utility\\Formatter::formatInteger' => ['integer', 'biginteger'],
            '\\Database\\Utility\\Formatter::formatDecimal' => ['decimal', 'float', 'numeric']
        ]
    ];

    /**
     * Are the formatters already loaded ?
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * Live cache where formatters and fields will be loaded.
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
     * Return the cache key name for the current table, language and domain.
     *
     * @return string
     */
    protected function cacheKey()
    {
        if ($this->cacheKey === null) {
            $plugin = Inflector::underscore(CodeLogic::root(__CLASS__));
            $class = Inflector::underscore(CodeLogic::tail(__CLASS__));
            $connection = Inflector::underscore($this->_table->connection()->configName());
            $table = Inflector::underscore($this->_table->table());
            $this->cacheKey = $plugin . '_' . $class . '_' . $connection . '_' . $table;
        }

        return $this->cacheKey;
    }

    /**
     * Returns all fields matching the regexp (or not).
     *
     * @param string $regexp The regexp to compare against
     * @param bool $expected Does the regexp need match or unmatch ?
     * @return array
     */
    protected function fieldsByRegexp($regexp, $expected = true)
    {
        $fields = [];

        foreach ($this->_table->schema()->columns() as $column) {
            if ((preg_match($regexp, $column) === 1) === $expected) {
                $fields[] = $column;
            }
        }

        return $fields;
    }

    /**
     * Returns all fields whose type matches (or not).
     *
     * @param string $type The type to compare against
     * @param bool $expected Does the type need match or unmatch ?
     * @return array
     */
    protected function fieldsByType($type, $expected = true)
    {
        $fields = [];

        foreach ($this->_table->schema()->columns() as $column) {
            $infos = $this->_table->schema()->column($column);
            if (($infos['type'] === $type) === $expected) {
                $fields[] = $column;
            }
        }

        return $fields;
    }

    /**
     * Returns the fields matching (or not) the provided conditions.
     *
     * @param mixed $conditions The conditions to match.
     * @param bool $expected Do conditions need to match or unmatch ?
     * @return array
     */
    protected function fieldsByConditions($conditions, $expected = true)
    {
        if (is_array($conditions) && isset($conditions['NOT'])) {
            return $this->fieldsByConditions($conditions['NOT'], !$expected);
        }

        $fields = [];

        if (is_bool($conditions)) {
            if ($conditions === $expected) {
                $fields = $this->_table->schema()->columns();
            } else {
                $fields = [];
            }
        } elseif (is_string($conditions)) {
            if (strpos($conditions, '/') === 0) {
                $fields = $this->fieldsByRegexp($conditions, $expected);
            } else {
                $fields = $this->fieldsByType($conditions, $expected);
            }
        } elseif (is_array($conditions)) {
            foreach ($conditions as $condition) {
                $fields = array_merge($fields, $this->fieldsByConditions($condition, $expected));
            }
        }

        return $fields;
    }

    /**
     * Returns the targeted field names for all configured formatter, live and
     * normal caching.
     *
     * If a formatter cannot be found, it will silently be removed from the list.
     *
     * @return array
     */
    protected function formatters()
    {
        if ($this->loaded === false) {
            $cacheKey = $this->cacheKey();
            $cache = Cache::read($cacheKey);

            if ($this->config('cache') === false || $cache === false) {
                $cache = [];

                foreach ((array)$this->config('formatters') as $callback => $conditions) {
                    $tokens = preg_split('/::/', $callback);
                    if (method_exists($tokens[0], $tokens[1])) {
                        $cache[$callback] = $this->fieldsByConditions($conditions);
                    }
                }

                if ($this->config('cache') !== false) {
                    Cache::write($cacheKey, $cache);
                }
            }

            $this->cache = $cache;
            $this->loaded = true;
        }

        return $this->cache;
    }

    /**
     * Format the entity fields with the configured formatters before data
     * validation or saving.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Cake\Event\Event $event The calling event
     * @param ArrayObject $data The available data
     * @param ArrayObject $options Marshalling options
     * @return void
     */
    public function beforeMarshal(\Cake\Event\Event $event, ArrayObject $data, ArrayObject $options)
    {
        foreach ($this->formatters() as $callback => $fields) {
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = call_user_func_array(preg_split('/::/', $callback), [$data[$field]]);
                }
            }
        }
    }
}
