<?php

/**
 * Source code for the Database.CacheKey unit test class.
 *
 */
namespace Database\Test\TestCase\Utility\CodeLogic;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Database\Utility\CodeLogic\CacheKey;

/**
 * The class Database.CacheKeyTest is responsible for testing the
 * Database.CacheKey utility class.
 */
class CacheKeyTest extends TestCase
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
     * Tests for the CacheKey::behavior method.
     *
     * @return void
     * @covers Database\Utility\CodeLogic\CacheKey::behavior
     */
    public function testBehavior()
    {
        $items = TableRegistry::get('Items');
        $items->addBehavior('DatabaseAutovalidate', ['className' => 'Database.Autovalidate']);
        $items->addBehavior('DatabaseFormattable', ['className' => 'Database.Formattable']);
        $items->addBehavior('Timestamp', ['className' => 'Timestamp']);

        $result = CacheKey::behavior($items->behaviors()->get('DatabaseAutovalidate'));
        $this->assertEquals('plugin_database_autovalidate_behavior', $result);

        $result = CacheKey::behavior($items->behaviors()->get('DatabaseFormattable'));
        $this->assertEquals('plugin_database_formattable_behavior', $result);

        $result = CacheKey::behavior($items->behaviors()->get('Timestamp'));
        $this->assertEquals('cake_timestamp_behavior', $result);
    }
}
