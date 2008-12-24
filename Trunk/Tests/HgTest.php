<?php

	require_once 'PHPUnit/TestCase.php';
	require_once 'VersionControl/Hg.php';

	class HgTest extends PHPUnit_TestCase
	{
		private $_object;

		public function setUp()
		{
			$this->_object = new Hg();
		}

		public function testSetPath() {}
		public function testGetPath() {}

		public function testFindRepository() {}
		public function testInitRepository() {}
		public function testDeleteRepository() {}

		public function tearDown() {}

	}

?>