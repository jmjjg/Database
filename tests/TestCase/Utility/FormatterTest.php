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
     * Tests for the Formatter::formatTrim() method.
     */
    public function testFormatTrim()
    {
        $this->assertEquals('Foo', Formatter::formatTrim('Foo'));
        $this->assertEquals('Foo', Formatter::formatTrim(" Foo\t\n"));
        $this->assertEquals(false, Formatter::formatTrim(false));
    }

    /**
     * Test for the Formatter::formatNull() method.
     */
    public function testFormatNull()
    {
        $this->assertEquals(null, Formatter::formatNull(''));
        $this->assertEquals(' ', Formatter::formatNull(' '));
        $this->assertEquals(false, Formatter::formatNull(false));
    }

    /**
     * Test for the Formatter::formatSuffix() method.
     */
    public function testFormatSuffix()
    {
        $this->assertEquals('6', Formatter::formatSuffix('6'));
        $this->assertEquals('', Formatter::formatSuffix('_'));
        $this->assertEquals('5', Formatter::formatSuffix('1_5'));
        $this->assertEquals('7', Formatter::formatSuffix('1_5_7'));
    }

    /**
     * Test for the Formatter::formatInteger() method.
     */
    public function testFormatInteger()
    {
        ini_set('intl.default_locale', 'fr_FR');
        $this->assertEquals('6', Formatter::formatInteger('6'));
        $this->assertEquals('Foo', Formatter::formatInteger('Foo'));
        $this->assertEquals('6.35', Formatter::formatInteger('6.35'));
        $this->assertEquals('1234567', Formatter::formatInteger('1 234 567,891'));

        ini_set('intl.default_locale', 'de_DE');
        $this->assertEquals('6', Formatter::formatInteger('6'));
        $this->assertEquals('Foo', Formatter::formatInteger('Foo'));
        $this->assertEquals('6.35', Formatter::formatInteger('6.35'));
        $this->assertEquals('1234567', Formatter::formatInteger('1.234.567,891'));
    }

    /**
     * Test for the Formatter::formatDecimal() method.
     */
    public function testFormatDecimal()
    {
        ini_set('intl.default_locale', 'fr_FR');
        $this->assertEquals('6', Formatter::formatDecimal('6'));
        $this->assertEquals('Foo', Formatter::formatDecimal('Foo'));
        $this->assertEquals('6.35', Formatter::formatDecimal('6.35'));
        $this->assertEquals('1234567.891', Formatter::formatDecimal('1 234 567,891'));

        ini_set('intl.default_locale', 'de_DE');
        $this->assertEquals('6', Formatter::formatDecimal('6'));
        $this->assertEquals('Foo', Formatter::formatDecimal('Foo'));
        $this->assertEquals('6.35', Formatter::formatDecimal('6.35'));
        $this->assertEquals('1234567.891', Formatter::formatDecimal('1.234.567,891'));
    }
}
