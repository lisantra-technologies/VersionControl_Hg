<?php

	require_once 'PHPUnit/Framework.php'; //PHPUnit/Framework/TestCase.php
	require_once 'H:/Development/_Webroot/Trunk/VersionControl/Hg.php';

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

		public function testSetPathWithEmptyString()
		{
			try {
				//this should raise an exception.
				$this->_object->setPath( '' );
			}
			catch(Exception $e) {
				return;
			}

			$this->fail('An expected exception has not been raised.');
		}

		public function testSetPathWithNoArguments()
		{
			try {
				//this should raise an exception.
				$this->_object->setPath();
			}
			catch(Exception $e) {
				return;
			}

			$this->fail('An expected exception has not been raised.');
		}

		public function testGetPath()
		{
			//set the path first.
			$result = $this->_object->setPath( $this->_path );
			//now run the test.
			$this->assertEquals( $this->_path, $this->_object->getPath() );
		}

		public function testIsRepository()
		{
			// Remove the following lines when you implement this test.
	        $this->markTestIncomplete(
	          'This test has not been implemented yet.'
	        );
		}

		public function testInitRepository()
		{
			// Remove the following lines when you implement this test.
	        $this->markTestIncomplete(
	          'This test has not been implemented yet.'
	        );
		}

		public function testDeleteRepository()
		{
			// Remove the following lines when you implement this test.
	        $this->markTestIncomplete(
	          'This test has not been implemented yet.'
	        );
		}

		public function tearDown() {}

	}

?>