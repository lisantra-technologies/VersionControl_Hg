<?php

    /*
     * $repo = $m->repo()->load('/path/');
     * $all = $repo->status()->all();
     * OR $all = $repo->command::status('all')->excluding('*.phtml');
     *
     * $m->repo::load('/path/')->status()->modified()->excluding('*.phtml');
     * $m->repo::load('/path/')->status('all'); this is implemented with __call in Repository.php
     * $m->repo::load('/path/')->command::init()->status('all');
     * $m->repo::load('/path/')->command()->status('all');
     *
     * in order to chain _exclude(), doesn't Status have to inherit Command?
     * $m->repo::load('/path/')->status('all')->_exclude('*.phtml');
     */

    /**
     *
     * Does not implement the following Mercurial 1.1.1 options:
     * -n --no-status  hide status prefix
     * -C --copies     show source of copied files
     * -0 --print0     end filenames with NUL, for use with xargs
     * --rev        show difference from revision
     *
     * Usage:
     * $hg = new Hg();
     * $hg->getRepository('/path/')->status('all');
     *
     * @author Michael Gatto <mgatto@u.arizona.edu>
     * @package VersionControl_Hg
     * @subpackage Commands
     */
	class Status extends Command
	{

	    /**
	     * The object which handles global options for every Mercurial command.
	     *
	     * No accessors are needed for this variable.
	     *
	     * @var Command
	     */
	    private $_wrapper;

	    /**
	     * Defaults the Mercurial command to be executed.
	     *
	     * @var string
	     */
	    private $_command = 'status'; //str_lower( get_class( $this ) ); OR str_lower( __CLASS__ );

	    /**
	     * Modifies the status output by restricting the type of tracked entities to report on.
	     * Its an array because parent::_execute expects an array.
	     *
	     * A restriction is one of:
	     * all        show status of all files
		 * modified   show only modified files
		 * added      show only added files
		 * removed    show only removed files
		 * deleted    show only deleted (but tracked) files
		 * clean      show only files without changes
		 * unknown    show only unknown (not tracked) files
		 * ignored    show only ignored files
		 * This package passes only the long-form options to the Mercurial executable
		 *
	     *
	     * @var array
	     * @see Command::setOption()
	     */
	    private $_restriction = array( 0 => 'all'); //default of 'all'

	    /**
	     * Sets the restriction passed into the command.
	     *
	     * @param $wrapper is the passed object / class instance of Command.php
	     * @param $restriction is required
	     * @return Collection
	     */
	    public function __construct( $restriction )
	    {
	        $this->setRestriction( $restriction );
        }

        /**
         * Return the status of entities in the repository subject to restrictions.
         *
		 * Status codes are returned as:
		 * M = modified
		 * A = added
		 * R = removed
		 * C = clean
		 * ! = deleted, but still tracked
		 * ? = not tracked
		 * I = ignored
         *
         * @return array where array[0] is the status code and array[1] is the entity's full path.
         * @see $_restriction
         */
	    public function getStatus()
	    {
	        /*
	         * parent::setOption expects a field name and a value, but status' options need no value
	         */
            parent::setOption( $this->getRestriction(), NULL );

            $result = parent::_execute( $this->_command );

    		//@todo abstract the field names into a class variable.
            $output = parent::parseOutput( array('status','entity'), $result );

            return $output;
	    }

	    /**
	     * Return the currently set restriction on the status command.
	     *
	     * @return string is the restriction
	     */
        private function getRestriction()
        {
            return $this->_restriction;
            //@todo what if its not set before getRestriction is called?
        }

        /**
         * Validates and sets the restriction for checking the status of repo entities.
         *
         * @param $restriction string
         * @return void
         * @see $_restriction
         */
        private function setRestriction( $restriction )
        {
            /*
             * if its empty, getRestriction() will return the default specified in the head of the class: 'all'
             */
            if( empty( $restriction ) ) {
                //throw new PEAR_Exception();
                return false; //halt processing
            }

            /*
             * check to ensure its a valid `hg status` option
             */
            $valid_restrictions = array( 'all','modified','added','removed','deleted','clean','unknown','ignored');
            if ( ! in_array( $restriction, $valid_restrictions ) ) {
                //throw new PEAR_Exception();
                return false; //halt processing
            }

            $this->_restriction = strtolower( $restriction );

        }

    }
?>