<?php
namespace Database\Test\Fixture;

use Cake\ORM\Table;

/**
 * Table used for unit test.
 */
class ItemsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('items');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo(
            'Parents',
            [
                'className' => 'Items',
                'foreignKey' => 'parent_id',
                'joinType' => 'LEFT OUTER'
            ]
        );
    }
}
