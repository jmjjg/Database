<?php

/**
 * Source code for the Database.ConfigureKey unit test class.
 *
 */
namespace Database\Test\TestCase\Utility\CodeLogic;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Database\Utility\CodeLogic\ConfigureKey;

/**
 * The class Database.ConfigureKeyTest is responsible for testing the
 * Database.ConfigureKey utility class.
 */
class ConfigureKeyTest extends TestCase
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
     * Tests for the ConfigureKey::fqn method.
     *
     * @return void
     * @covers Database\Utility\CodeLogic\ConfigureKey::fqn
     */
    public function testFqn()
    {
        $items = TableRegistry::get('Items');
        $items->addBehavior('DatabaseAutovalidate', ['className' => 'Database.Autovalidate']);
        $items->addBehavior('DatabaseFormattable', ['className' => 'Database.Formattable']);
        $items->addBehavior('Timestamp', ['className' => 'Timestamp']);

        $result = ConfigureKey::fqn($items->behaviors()->get('DatabaseAutovalidate'));
        $this->assertEquals('plugin.Database.AutovalidateBehavior', $result);

        $result = ConfigureKey::fqn($items->behaviors()->get('DatabaseFormattable'));
        $this->assertEquals('plugin.Database.FormattableBehavior', $result);

        $result = ConfigureKey::fqn($items->behaviors()->get('Timestamp'));
        $this->assertEquals('Cake.TimestampBehavior', $result);
    }
}
