<?php
/**
 * Source file for the ItemsFixture class.
 */
namespace Database\Test\Fixture;

use Cake\Datasource\ConnectionInterface;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * The ItemsFixture class will create the fixture for the items table,
 * adding SQL CHECK constraints to some fields.
 */
class ItemsFixture extends TestFixture
{
    /**
     * Fields
     *
     * Postgres -> CakePHP@3.1.0~rc1
     * date         date
     * time         time
     * boolean      boolean
     * timestamp    timestamp
     * *timestamp*  timestamp
     * *time*       time
     * serial       integer(10)
     * integer      integer(10)
     * bigserial    integer(20)
     * bigint       integer(20)
     * smallint     integer(5)
     * inet         string(39)
     * uuid         uuid
     * char(x)      string(x), fixed
     * character    string(x), fixed
     * *char*       string(x)
     * *money*      string(x)
     * *text*       text
     * bytea        binary
     * real         float
     * *double*     float
     * *numeric*    decimal
     * *decimal*    decimal
     * *            text
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'autoIncrement' => true, 'default' => null, 'null' => false, 'item' => null, 'precision' => null, 'unsigned' => null],
        'name' => ['type' => 'string', 'length' => 255, 'default' => null, 'null' => false, 'item' => null, 'precision' => null, 'fixed' => null],
        'parent_id' => ['type' => 'integer', 'length' => 10, 'default' => null, 'null' => true, 'item' => null, 'precision' => null, 'unsigned' => null, 'autoIncrement' => null],
        'visible' => ['type' => 'boolean', 'default' => true, 'null' => false],
        'sold' => ['type' => 'integer', 'default' => 0, 'null' => false],
        'price' => ['type' => 'float', 'default' => null, 'null' => false],
        'weight' => ['type' => 'decimal', 'default' => null, 'null' => false],
        'birthday' => ['type' => 'date', 'default' => null, 'null' => true],
        'alarm' => ['type' => 'time', 'default' => null, 'null' => true],
        'uuid' => ['type' => 'uuid', 'default' => null, 'null' => true],
        'biginteger' => ['type' => 'biginteger', 'default' => null, 'null' => true],
        'reminder' => ['type' => 'datetime'],
        'numeric' => ['type' => 'decimal', 'length' => 5, 'default' => null, 'null' => true, 'precision' => 2],
        'created' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'item' => null, 'precision' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'item' => null, 'precision' => null],
        '_indexes' => [
            'items_parent_id_idx' => ['type' => 'index', 'columns' => ['parent_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'items_name_idx' => ['type' => 'unique', 'columns' => ['name']],
            'items_parent_id_fk' => [
                'type' => 'foreign',
                'columns' => ['parent_id'],
                'references' => ['items', 'id'],
                'update' => 'cascade',
                'delete' => 'cascade'
            ]
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [];
}
