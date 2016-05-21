<?php
/**
 * Utility functions for the Database plugin.
 */
namespace Database;

/**
 * Returns the root namespace from a namespaced class name or null.
 *
 * @param string $namespace A namespaced class name
 * @return string
 */
function namespaceRoot($namespace)
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
function namespaceTail($namespace)
{
    $position = strrpos($namespace, '\\');
    return $position === false ? $namespace : substr($namespace, $position + 1);
}
