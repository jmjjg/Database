<?php
/**
 * Source code for the Database.Fqn unit test class.
 *
 */
namespace Database\Test\TestCase\Utility\CodeLogic;

use Cake\TestSuite\TestCase;
use Database\Utility\CodeLogic\Fqn;

/**
 * The class Database.FqnTest is responsible for testing the
 * Database.Fqn utility class.
 */
class FqnTest extends TestCase
{

    /**
     * Tests for the Fqn::root method.
     *
     * @return void
     * @covers Database\Utility\CodeLogic\Fqn::root
     */
    public function testRoot()
    {
        // No namespace
        $this->assertEquals(null, Fqn::root('Foo'));

        // A namespace
        $this->assertEquals('Foo', Fqn::root('Foo\\Bar\\Baz'));
    }

    /**
     * Tests for the Fqn::tail method.
     *
     * @return void
     * @covers Database\Utility\CodeLogic\Fqn::tail
     */
    public function testTail()
    {
        // No namespace
        $this->assertEquals('Foo', Fqn::tail('Foo'));

        // A namespace
        $this->assertEquals('Baz', Fqn::tail('Foo\\Bar\\Baz'));
    }
}
