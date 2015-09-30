<?php
/**
 * Source file for the basic functions from the Database plugin.
 */
namespace Database\Test\TestCase;

use Cake\TestSuite\TestCase;

/**
 * BasicsTest class
 */
class BasicsTest extends TestCase
{

    /**
     * Tests for the namespaceRoot() function.
     *
     * @return void
     */
    public function testNamespaceRoot()
    {
        // No namespace
        $this->assertEquals(null, namespaceRoot('Foo'));

        // A namespace
        $this->assertEquals('Foo', namespaceRoot('Foo\\Bar\\Baz'));
    }

    /**
     * Tests for the namespaceTail() function.
     *
     * @return void
     */
    public function testNamespaceTail()
    {
        // No namespace
        $this->assertEquals('Foo', namespaceTail('Foo'));

        // A namespace
        $this->assertEquals('Baz', namespaceTail('Foo\\Bar\\Baz'));
    }
}
