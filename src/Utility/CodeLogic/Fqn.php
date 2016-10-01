<?php

/**
 * Source code for the Database.Fqn class.
 *
 */
namespace Database\Utility\CodeLogic;

use Cake\Utility\Inflector;

/**
 * The Fqn class provides functions for OOP code logic.
 */
abstract class Fqn
{

    /**
     * A live cache.
     *
     * @var array
     */
    protected static $liveCache = [];

    /**
     * The namespace names that will not begin with "plugin".
     *
     * @var array
     */
    protected static $notPlugin = ['App', 'Cake'];

    /**
     * Returns the root namespace fully qualified classname or null.
     *
     * @param string $fqn The fully-qualified classname
     * @return string
     */
    public static function root($fqn)
    {
        if (false === isset(static::$liveCache[__FUNCTION__][$fqn])) {
            $position = strpos($fqn, '\\');
            static::$liveCache[__FUNCTION__][$fqn] = $position === false ? null : substr($fqn, 0, $position);
        }

        return static::$liveCache[__FUNCTION__][$fqn];
    }

    /**
     * Returns the fully qualified classname without any namespace.
     *
     * @param string $fqn The fully-qualified classname
     * @return string
     */
    public static function tail($fqn)
    {
        if (false === isset(static::$liveCache[__FUNCTION__][$fqn])) {
            $position = strrpos($fqn, '\\');
            static::$liveCache[__FUNCTION__][$fqn] = $position === false ? $fqn : substr($fqn, $position + 1);
        }

        return static::$liveCache[__FUNCTION__][$fqn];
    }

    /**
     * Returns the prefix (empty for App and Cake, "plugin_" otherwise) from a fully qualified classname.
     *
     * @param string $fqn The fully-qualified classname
     * @return string
     */
    public static function prefix($fqn)
    {
        if (false === isset(static::$liveCache[__FUNCTION__][$fqn])) {
            static::$liveCache[__FUNCTION__][$fqn] = false === in_array(static::root($fqn), static::$notPlugin)
                ? 'plugin'
                : '';
        }

        return static::$liveCache[__FUNCTION__][$fqn];
    }
}
