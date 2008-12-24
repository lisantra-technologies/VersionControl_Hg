<?php

	require_once 'PHPUnit/Framework.php'; //PHPUnit/Framework/TestCase.php
	require_once 'VersionControl/Hg.php';

	class HgTest extends PHPUnit_Framework_TestCase
	{
		private $_object;
		private $_path;

		public function setUp()
		{
			$this->_path = 'H:/Development/_Webroot/Trunk/Tests/Fixtures/Test_Repository';
			$this->_object = new Hg();
		}

		public function testSetPath()
		{
			$this->assertTrue( $this->_object->setPath( $this->_path ) );
		}

		public function testGetPath()
		{
			$this->assertEquals( $this->_path, $this->_object->getPath() );
		}

		public function testFindRepository() {}
		public function testInitRepository() {}
		public function testDeleteRepository() {}

		public function tearDown() {}

	}

?>