<?php

/**
 * Source code for the Database.PluginConfigTrait trait.
 *
 */
namespace Database\Core;

use Cake\Core\Configure;
use Database\Utility\CodeLogic\ConfigureKey;

/**
 * This trait provides a protected method that checks for cache with the
 * Cake\Core\InstanceConfigTrait::config method.
 */
trait PluginConfigTrait
{

    /**
     * Checks if the cache is to be used by checking the class'
     * Cake\Core\InstanceConfigTrait::config method and; if it is unset (NULL),
     * then the configuration key will be read. If it is still unset, the "debug"
     * Configure path will be checked (TRUE in production mode).
     *
     * @return bool
     */
    protected function useCache()
    {
        $useCache = $this->config('cache');

        if (null === $useCache) {
            $configureKey = ConfigureKey::fqn($this) . '.cache';
            $useCache = Configure::read($configureKey);
        }

        $result = null === $useCache ? false === Configure::read('debug') : (bool)$useCache;
        return $result;
    }
}
