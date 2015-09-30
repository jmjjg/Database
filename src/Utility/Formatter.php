<?php
/**
 * Source code for the Database.AutovalidateBehavior class.
 *
 */
namespace Database\Utility;

/**
 * This class provides utility formatter functions to use on database field values,
 * namely with the FormattableBehavior from this plugin.
 */
abstract class Formatter
{
    /**
     * Returns a trimmed string if the value type is string, the original value
     * otherwise.
     *
     * @param mixed $value The value to trim
     * @return mixed
     */
    public static function trim($value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * Returns null if the value is an empty string, the original value otherwise.
     *
     * @param mixed $value The value to nullify
     * @return mixed
     */
    public static function null($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    /**
     * Returns the suffix (the part after the last separator) of the string if
     * the type of the value is a string, the original value otherwise.
     *
     * @param mixed $value The value whose suffix is needed
     * @param string $separator The string separator
     * @return mixed
     */
    public static function suffix($value, $separator = '_')
    {
        if (is_string($value)) {
            $position = strrpos($value, $separator);
            if ($position !== false) {
                if ($position + 1 < strlen($value)) {
                    $value = substr($value, $position + 1);
                } else {
                    $value = '';
                }
            }
        }

        return $value;
    }

    /**
     * Returns the original value if is an integer, the current locale's parsed
     * integer value or the original value if it cannot be parsed.
     *
     * @see intl.default_locale
     *
     * @param mixed $value The value to parse
     * @return mixed
     */
    public static function integer($value)
    {
        if (is_int($value) === false) {
            $formatter = new \NumberFormatter(ini_get('intl.default_locale'), \NumberFormatter::INTEGER_DIGITS);
            $result = $formatter->parse($value);
            $value = ($result === false) ? $value : (int)$result;
        }

        return $value;
    }

    /**
     * Returns the original value if is a numeric value, the current locale's parsed
     * decimal value or the original value if it cannot be parsed.
     *
     * @see intl.default_locale
     *
     * @param mixed $value The value to parse
     * @return mixed
     */
    public static function decimal($value)
    {
        if (is_numeric($value) === false) {
            $formatter = new \NumberFormatter(ini_get('intl.default_locale'), \NumberFormatter::DECIMAL);
            $result = $formatter->parse($value);
            $value = ($result === false) ? $value : $result;
        }

        return $value;
    }
}
