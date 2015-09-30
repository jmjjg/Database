<?php
/**
 * Source code for the Database.Formatter unit test class.
 *
 */
namespace Database\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use Database\Utility\Formatter;

/**
 * The class Database.FormatterTest is responsible for testing the
 * Database.Formatter utility class.
 */
class FormatterTest extends TestCase
{
    /**
     * Original intl.default_locale.
     *
     * @var string
     */
    public $default_locale = null;

    /**
     * Executed before each test method, store the original default_locale value.
     */
    public function setUp()
    {
        parent::setUp();
        $this->default_locale = ini_get('intl.default_locale');
    }

    /**
     * Executed after each test method, restore the original default_locale value.
     */
    public function tearDown()
    {
        parent::tearDown();
        ini_set('intl.default_locale', $this->default_locale);
    }

    /**
     * Tests for the Formatter::trim() method.
     */
    public function testTrim()
    {
        $this->assertEquals('Foo', Formatter::trim('Foo'));
        $this->assertEquals('Foo', Formatter::trim(" Foo\t\n"));
        $this->assertEquals(false, Formatter::trim(false));
    }

    /**
     * Test for the Formatter::null() method.
     */
    public function testNull()
    {
        $this->assertEquals(null, Formatter::null(''));
        $this->assertEquals(' ', Formatter::null(' '));
        $this->assertEquals(false, Formatter::null(false));
    }

    /**
     * Test for the Formatter::suffix() method.
     */
    public function testSuffix()
    {
        $this->assertEquals('6', Formatter::suffix('6'));
        $this->assertEquals('', Formatter::suffix('_'));
        $this->assertEquals('5', Formatter::suffix('1_5'));
        $this->assertEquals('7', Formatter::suffix('1_5_7'));
    }

    /**
     * Test for the Formatter::integer() method.
     */
    public function testInteger()
    {
        ini_set('intl.default_locale', 'fr_FR');
        $this->assertEquals('6', Formatter::integer('6'));
        $this->assertEquals('Foo', Formatter::integer('Foo'));
        $this->assertEquals('6.35', Formatter::integer('6.35'));
        $this->assertEquals('1234567', Formatter::integer('1 234 567,891'));

        ini_set('intl.default_locale', 'de_DE');
        $this->assertEquals('6', Formatter::integer('6'));
        $this->assertEquals('Foo', Formatter::integer('Foo'));
        $this->assertEquals('6.35', Formatter::integer('6.35'));
        $this->assertEquals('1234567', Formatter::integer('1.234.567,891'));
    }

    /**
     * Test for the Formatter::decimal() method.
     */
    public function testDecimal()
    {
        ini_set('intl.default_locale', 'fr_FR');
        $this->assertEquals('6', Formatter::decimal('6'));
        $this->assertEquals('Foo', Formatter::decimal('Foo'));
        $this->assertEquals('6.35', Formatter::decimal('6.35'));
        $this->assertEquals('1234567.891', Formatter::decimal('1 234 567,891'));

        ini_set('intl.default_locale', 'de_DE');
        $this->assertEquals('6', Formatter::decimal('6'));
        $this->assertEquals('Foo', Formatter::decimal('Foo'));
        $this->assertEquals('6.35', Formatter::decimal('6.35'));
        $this->assertEquals('1234567.891', Formatter::decimal('1.234.567,891'));
    }
}
