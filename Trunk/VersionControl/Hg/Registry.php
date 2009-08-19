<?php

/**
 * Contains the definition of the Registry class
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Repository
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link 		http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Registers objects used in this package.
 *
 * This pattern avoids mutual references between objects and maintains
 * state.
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Repository
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Registry
{

    /**
     * Stores an array of objects
     *
     * @var mixed
     */
    private $_objects = array();

    /**
     * To implement the singleton registry pattern
     *
     * @var VersionControl_Hg_Registry
     */
    private static $_instance;

    /**
     * Constructor
     *
     * Private to implement the singleton registry pattern
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Implements the singleton registry pattern
     *
     * @return VersionControl_Hg_Registry
     */
    public static function getInstance()
    {
        if ( null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Stores an object identified by the name parameter.
     *
     * @param   string $name defaults to $object's class name
     * @param   mixed $object
     * @return  bool
     */
    public function set($name = null, $object)
    {
        if ($this->has($name)) {
            //don't overwrite
            return;
        }

        //if the name is null, attempt to recover
        if (is_object($object) && is_null($name)) {
            $name = get_class($object);
        }

        //evaluates as boolean (?)
        return $this->_objects[$name] = $object;
    }

    /**
     * Checks if the given key is currently stored
     *
     * @param   string $name is the key in the array referencing an object
     * @return  bool
     */
    public function has($name)
    {
        //array_key_exists returns bool, thus this shortcut
        return array_key_exists($name, $this->_objects);
    }

    /**
     *
     * @param   string $name
     * @return  object
     */
    public function get($name)
    {
        if (! $this->has($name)) {
            throw new VersionControl_Hg_Exception(
                "I have not registered: ${name}"
            );
        }

        return $this->_objects[$name];
    }

    /**
     * Prevent users to clone the instance
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

}
