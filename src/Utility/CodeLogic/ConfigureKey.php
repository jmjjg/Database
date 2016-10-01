<?php
/**
 * Source code for the Database.ConfigureKey class.
 */
namespace Database\Utility\CodeLogic;

use Database\Utility\CodeLogic\Fqn;

/**
 * The ConfigureKey class provides helper functions for configure keys naming
 * coherence.
 */
abstract class ConfigureKey
{
    /**
     * A live cache.
     *
     * @var array
     */
    protected static $liveCache = [];

    /**
     * Returns the configure key prefix to be used with any class.
     * It consists of a suffix ("plugin" if the namesapce is not App or Cake),
     * the namespace and the class name, separated by dots.
     *
     * Examples
     * - \App\View\Helper\ResultsHelper would give App.ResultsHelper
     * - \Cake\View\Helper\HtmlHelper would give Cake.HtmlHelper
     * - \Database\Model\Behavior\FormattableBehavior would give plugin.Database.FormattableBehavior
     *
     * @param Oject $class The class whose configure prefix is needed
     * @return string
     */
    public static function fqn($class)
    {
        $alias = get_class($class);
        if (false === isset(static::$liveCache[__FUNCTION__][$alias])) {
            $root = Fqn::root($alias);
            static::$liveCache[__FUNCTION__][$alias] = implode(
                '.',
                array_filter(
                    [
                        Fqn::prefix($alias),
                        $root,
                        Fqn::tail($alias)
                    ]
                )
            );
        }

        return static::$liveCache[__FUNCTION__][$alias];
    }
}
