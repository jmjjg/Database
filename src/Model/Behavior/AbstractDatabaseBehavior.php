<?php
/**
 * Source code for the Database.AutovalidateBehavior class.
 *
 */
namespace Database\Model\Behavior;

use Cake\ORM\Behavior;
use Database\Core\PluginConfigTrait;

/**
 * The class AbstractDatabaseBehavior is the base class for the behaviors in the
 * Database plugin.
 *
 * It uses the PluginConfigTrait trait which provides the protected useCache method.
 */
abstract class AbstractDatabaseBehavior extends Behavior
{
    use PluginConfigTrait;
}
