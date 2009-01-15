<?php
    require_once 'PHPUnit/Framework.php';

	require_once 'H:/Development/_Webroot/Trunk/VersionControl/Repository.php';

    /**
     * Test class for Repository.
     * Generated by PHPUnit on 2008-12-30 at 14:14:06.
     */
    class RepositoryTest extends PHPUnit_Framework_TestCase
    {
        /**
         * @var    Repository
         * @access protected
         */
        protected $_object;

		/**
		 *
		 * @var string
		 */
		private $_path;

        /**
         * Sets up the fixture, for example, opens a network connection.
         * This method is called before a test is executed.
         *
         * @access protected
         */
        protected function setUp()
        {
            $this->_path = 'H:/Development/_Webroot/Trunk/Tests/Fixtures/Test_Repository';

            $this->object = new Repository();
        }

        /**
         * Tears down the fixture, for example, closes a network connection.
         * This method is called after a test is executed.
         *
         * @access protected
         */
        protected function tearDown()
        {
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

		public function testSetPathWithBogusDirectory()
		{
			try {
				//this should raise an exception.
				$this->_object->setPath( 'H:\xyz\_Webroot' );
			}
			catch(Exception $e) {
			    if ( strlen( $e->getMessage() ) > 1 ) {
			        print $e->getMessage();
				    return true;
			    } else {
			        $this->fail('An expected exception has not been raised.');
			    }
			}

		}

		public function testGetPath()
		{
			//set the path first.
			$result = $this->_object->setPath( $this->_path );
			//now run the test.
			$this->assertEquals( $this->_path, $this->_object->getPath() );
		}

		public function testGetPathWhenPathIsEmpty()
		{
			try {
				//this should raise an exception.
				$this->_object->getPath();
			}
			catch(Exception $e) {
			    if ( strlen( $e->getMessage() ) > 1 ) {
			        print $e->getMessage();
				    return true;
			    } else {
			        $this->fail('An expected exception has not been raised.');
			    }
			}
		}

    /**
         * @todo Implement testCreate().
         */
        public function testCreate() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testLoad().
         */
        public function testLoad() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testDelete().
         */
        public function testDelete() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testMove_to().
         */
        public function testMove_to() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testRename_to().
         */
        public function testRename_to() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testClone_from().
         */
        public function testClone_from() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testGetPath().
         */
        public function testGetPath() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
              'This test has not been implemented yet.'
            );
        }

        /**
         * @todo Implement testIsRepository().
         */
        public function testIsRepository() {
            //set the path first.
    		$result = $this->_object->setPath( $this->_path );
    		$this->assertTrue( $this->_object->isRepository() );
        }
    }
?>
