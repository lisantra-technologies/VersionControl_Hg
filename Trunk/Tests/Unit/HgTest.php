<?php

    require_once '../../VersionControl/Hg.php';

    /**
     * Test class for Hg.
     * Generated by PHPUnit on 2008-12-24 at 13:06:43.
     */
    class HgTest extends PHPUnit_Framework_TestCase
    {
        /**
         *
         * @var Hg
         */
        private $_object;

        //public $basepath = "../Functional/";
        public $test_repository = 'H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository';
        public $invalid_repo = 'C:\Windows\Temp';
        public $nonexistant_path = 'C:\Temp';

        /**
         * Sets up the fixture, for example, opens a network connection.
         * This method is called before a test is executed.
         */
        protected function setUp() {}



        /**
         * Tears down the fixture, for example, closes a network connection.
         * This method is called after a test is executed.
         */
        public function tearDown() {
            $this->_object = null;
            unset($this->_object);
        }

    }
