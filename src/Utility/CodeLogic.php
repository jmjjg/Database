<?php
/**
 * Source code for the Database.CodeLogic class.
 *
 */
namespace Database\Utility;

/**
 * The CodeLogic class provides functions for OOP code logic.
 */
abstract class CodeLogic
{
    /**
     * Returns the root namespace from a namespaced class name or null.
     *
     * @param string $namespace A namespaced class name
     * @return string
     */
    public static function root($namespace)
    {
        $position = strpos($namespace, '\\');
        return $position === false ? null : substr($namespace, 0, $position);
    }

    /**
     * Returns the class name without any namespace.
     *
     * @param string $namespace A namespaced class name
     * @return string
     */
    public static function tail($namespace)
    {
        $position = strrpos($namespace, '\\');
        return $position === false ? $namespace : substr($namespace, $position + 1);
    }
}
