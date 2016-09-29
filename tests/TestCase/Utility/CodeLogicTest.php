<?php
/**
 * Source code for the Database.CodeLogic unit test class.
 *
 */
namespace Database\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use Database\Utility\CodeLogic;

/**
 * The class Database.CodeLogicTest is responsible for testing the
 * Database.CodeLogic utility class.
 */
class CodeLogicTest extends TestCase
{

    /**
     * Tests for the namespaceRoot() function.
     *
     * @return void
     */
    public function testRoot()
    {
        // No namespace
        $this->assertEquals(null, CodeLogic::root('Foo'));

        // A namespace
        $this->assertEquals('Foo', CodeLogic::root('Foo\\Bar\\Baz'));
    }

    /**
     * Tests for the namespaceTail() function.
     *
     * @return void
     */
    public function testTail()
    {
        // No namespace
        $this->assertEquals('Foo', CodeLogic::tail('Foo'));

        // A namespace
        $this->assertEquals('Baz', CodeLogic::tail('Foo\\Bar\\Baz'));
    }
}
