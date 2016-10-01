<?php

/**
 * Source code for the Database.CacheKey class.
 *
 */
namespace Database\Utility\CodeLogic;

use Cake\Utility\Inflector;
use Database\Utility\CodeLogic\Fqn;

/**
 * The CacheKey class provides helper functions for cache keys naming coherence.
 */
abstract class CacheKey
{

    /**
     * A live cache.
     *
     * @var array
     */
    protected static $liveCache = [];

    /**
     * Returns the cache key (prefix) to be used with a behavior class.
     * It doesn't include anything about the table the behavior is attached to.
     *
     * @see static::table
     *
     * @param \Cake\ORM\Behavior $behavior The behavior
     * @return string
     */
    public static function behavior(\Cake\ORM\Behavior $behavior)
    {
        $alias = get_class($behavior);
        if (false === isset(static::$liveCache[__FUNCTION__][$alias])) {
            static::$liveCache[__FUNCTION__][$alias] = implode(
                '_',
                array_filter(
                    [
                        Fqn::prefix($alias),
                        Inflector::underscore(Fqn::root($alias)),
                        Inflector::underscore(Fqn::tail($alias))
                    ]
                )
            );
        }

        return static::$liveCache[__FUNCTION__][$alias];
    }

    /**
     * Returns the cache key (prefix) to be used with a table class.
     *
     * @param \Cake\ORM\Table $table The table class
     * @return string
     */
    public static function table(\Cake\ORM\Table $table)
    {
        $alias = get_class($table);
        if (false === isset(static::$liveCache[__FUNCTION__][$alias])) {
            static::$liveCache[__FUNCTION__][$alias] = implode(
                '_',
                array_filter(
                    [
                        Inflector::underscore($table->connection()->configName()),
                        Fqn::prefix($alias),
                        Inflector::underscore(Fqn::root($alias)),
                        Inflector::underscore(Fqn::tail($alias)),
                        Inflector::underscore($table->alias()),
                    ]
                )
            );
        }

        return static::$liveCache[__FUNCTION__][$alias];
    }
}
