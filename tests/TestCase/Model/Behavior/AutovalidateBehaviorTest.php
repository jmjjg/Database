<?php

/**
 * Source code for the Database.AutovalidateBehavior unit test class.
 *
 */
namespace Database\Test\TestCase\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Database\Utility\CodeLogic\ConfigureKey;

require_once dirname(__FILE__) . DS . '..' . DS . '..' . DS . '..' . DS . 'Fixture' . DS . 'items_table.php';

/**
 * The class Database.AutovalidateBehaviorTest is responsible for testing the
 * Database.AutovalidateBehavior class.
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class AutovalidateBehaviorTest extends TestCase
{

    /**
     * fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Database.Items',
    ];

    /**
     * Live data.
     *
     * @var array
     */
    public $data = [
        'name' => 'Test',
        'price' => 666.66,
        'weight' => 33.33
    ];

    /**
     * Returns field informations for each field in the table.
     *
     * @return array
     */
    protected function expectedFieldInfos()
    {
        return [
            // Primary key (integer)
            'id' => [
                'isInteger' => [
                    'rule' => 'isInteger',
                    'on' => null,
                    'last' => false,
                    'message' => 'Veuillez entrer un nombre entier',
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => false
            ],
            // Biginteger
            'biginteger' => [
                'isInteger' => [
                    'rule' => 'isInteger',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::isInteger'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ],
            // Boolean
            'visible' => [
                'boolean' => [
                    'rule' => 'boolean',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::boolean'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => false
            ],
            // Date
            'birthday' => [
                'date' => [
                    'rule' => 'date',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::date'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ],
            // Datetime
            'reminder' => [
                'datetime' => [
                    'rule' => 'datetime',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::datetime'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ],
            // Timestamp
            'created' => [
                'datetime' => [
                    'rule' => 'datetime',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::datetime'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ],
            // Decimal
            'weight' => [
                'numeric' => [
                    'rule' => 'numeric',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::numeric'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => 'create'
            ],
            // Float
            'price' => [
                'numeric' => [
                    'rule' => 'numeric',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::numeric'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => 'create'
            ],
            // Integer
            'sold' => [
                'isInteger' => [
                    'rule' => 'isInteger',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::isInteger'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => false
            ],
            // Numeric
            'numeric' => [
                'numeric' => [
                    'rule' => 'numeric',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::numeric'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => false, // FIXME
                'isPresenceRequired' => false
            ],
            // String
            'name' => [
                'maxLength' => [
                    'rule' => 'maxLength',
                    'on' => null,
                    'last' => false,
                    'message' => sprintf(__d('database', 'Validate::maxLength'), 255),
                    'provider' => 'default',
                    'pass' => [255]
                ],
                'unique' => [
                    'rule' => 'validateUnique',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::isUnique'),
                    'provider' => 'table',
                    'pass' => []
                ],
                'isEmptyAllowed' => false,
                'isPresenceRequired' => 'create'
            ],
            // Time
            'alarm' => [
                'time' => [
                    'rule' => 'time',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::time'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ],
            // Uuid
            'uuid' => [
                'uuid' => [
                    'rule' => 'uuid',
                    'on' => null,
                    'last' => false,
                    'message' => __d('database', 'Validate::uuid'),
                    'provider' => 'default',
                    'pass' => []
                ],
                'isEmptyAllowed' => true,
                'isPresenceRequired' => false
            ]
        ];
    }
    // @codingStandardsIgnoreEnd

    protected function clear($alias, array $params = [])
    {
        $params += ['config' => true, 'cache' => true];

        if (true === $params['config']) {
            $key = $this->Items->behaviors()->get($alias)->cacheKey();
            Cache::delete($key);
        }

        if (true === $params['cache']) {
            $behavior = $this->Items->behaviors()->get($alias);
            $key = ConfigureKey::fqn($behavior);
            Configure::write($key, null);
        }
    }

    /**
     * setUp() method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Items = TableRegistry::get('Items');
        $this->Items->addBehavior('DatabaseAutovalidate', ['className' => 'Database.Autovalidate']);
    }

    /**
     * tearDown() method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Items);
    }

    /**
     * Utility function: returns the list named rules along with isEmptyAllowed,
     * isPresenceRequired and FIXME informations for a given field.
     *
     * @param string $field
     * @return array
     */
    protected function getFieldInfos($field)
    {
        $result = [];

        foreach ($this->Items->validator()->field($field)->rules() as $key => $rule) {
            $result[$key] = [
                'rule' => $rule->get('rule'),
                'on' => $rule->get('on'),
                'last' => $rule->get('last'),
                'message' => $rule->get('message'),
                'provider' => $rule->get('provider'),
                'pass' => $rule->get('pass')
            ];
        }

        $result['isEmptyAllowed'] = $this->Items->validator()->field($field)->isEmptyAllowed();
        $result['isPresenceRequired'] = $this->Items->validator()->field($field)->isPresenceRequired();

        return $result;
    }

    /**
     * Check the expected rules for all field types.
     *
     * @see expectedFieldInfos()
     * @return void
     */
    public function testFieldInfos()
    {
        $this->clear('DatabaseAutovalidate');
        // @fixme
        Configure::write('plugin.Database.AutovalidateBehavior', true);

        foreach ($this->expectedFieldInfos() as $fieldName => $expected) {
            $result = $this->getFieldInfos($fieldName);
            $this->assertEquals([$fieldName => $expected], [$fieldName => $result]);
        }
    }

    /**
     * Test that unique constraints are enforced on saving.
     *
     * INFO: here, the unique rule is checked, but not the constraint itself.
     */
    public function testIsUniqueConstraint()
    {
        $this->clear('DatabaseAutovalidate');

        // 1. First record to populate the database
        $item = $this->Items->newEntity($this->data);
        $this->assertTrue($this->Items->save($item) !== false);

        // 2. Test record with the same name
        $notUnique = $this->Items->newEntity($this->data);
//        $this->assertFalse($this->Items->rulesChecker()->checkCreate($notUnique));
        $this->assertFalse($this->Items->save($notUnique) !== false);

        $expected = [
//            '_isUnique' => __d('database', 'Validate::isUnique')
            'unique' => __d('database', 'Validate::isUnique')
        ];
        $this->assertEquals($notUnique->errors('name'), $expected);
    }

    /**
     * Test that foreign key constraints are enforced on saving.
     */
    public function testExistsInConstraint()
    {
        $this->clear('DatabaseAutovalidate');

        // 1. First record to populate the database
        $item = $this->Items->newEntity($this->data);
        $this->assertTrue($this->Items->save($item) !== false);

        // 2. Test record with the same name
        $notExistsIn = $this->Items->newEntity($this->data);
        $notExistsIn->parent_id = 2;
        $notExistsIn->name = 'Test 2';
        $this->assertFalse($this->Items->rulesChecker()->checkCreate($notExistsIn));
        $this->assertFalse($this->Items->save($notExistsIn) !== false);

        $expected = [
            '_existsIn' => __d('database', 'Validate::existsIn')
        ];
        $this->assertEquals($notExistsIn->errors('parent_id'), $expected);
    }

    // $this->clear('DatabaseAutovalidate');
}
