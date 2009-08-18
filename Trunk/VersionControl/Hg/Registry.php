<?php

class VersionControl_Hg_Registry
{

    private $_objects = array();

    private static $_instance;

    private function __construct() {}


    public static function getInstance()
    {
        if ( null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    public function set($name = null, $object)
    {
        if ($this->has($name)) {
            //oppps, don't overwrite
            return;
        }

        //if the name is null
        if (is_object($object) && is_null($name)) {
            $name = get_class($object);
        }

        $this->_objects[$name] = $object;
    }

    public function has($name)
    {
    /* $has = false;

        if () {
            $has = true;
        }*/
        //array_key_exists returns bool, thus this shortcut
        return array_key_exists($name, $this->_objects);
    }

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
