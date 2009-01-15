<?php
	/**
	 * @package VersionControl_Hg
	 * @license
	 * @copyright
	 * @version $Revision$
	 */

	/**
	 * Assumes to be working on a local filesystem repository
	 *
	 * @package VersionControl_Hg
	 * @author Michael Gatto <mgatto@u.arizona.edu>
	 *
	 * Usage:
	 * $hg = new hg();
	 *
	 */
	class Hg
	{
		/**
		 * The version of the Mercurial executable.
		 *
		 * @var float
		 */
		private $_version;

		/**
		 *
		 * @return
		 */
		public function __construct()
			{
			    $this->setVersion();

			}

        /**
         *
         * @return string ?
         */
		public function getVersion()
		{
            return $this->_version;
		}

		/**
		 * Returns the version of the Mercurial executable.
		 *
		 * Implements the --version switch of the command-line client.
		 * Possible values are:
		 * (version 1.1), (version 1.1+20081220), (version 1.1+e54ac289bed), (unknown)
		 *
		 * @return array
		 * @see $_version
		 */
		public function setVersion( $version_data = NULL )
			{
			    /*
			     * set input source
			     */
			    if ( $version_data === NULL ) {
				    exec( 'hg --version', $output );
				    $ver_string = $output[0];
			    } else {
			        $ver_string = $version_data;
			    }

                /*
                 * handle bad input
                 */
                if ( preg_match( '/\(.*\)/', $ver_string, $ver_match ) == 0 ) {
                    throw new Exception('Unrecognized version data');
                }

                //this is only processed if no exception.
                $version['raw'] = trim( substr( $ver_match[0], 8, strlen( $ver_match[0] ) ) );
                //replace the parenthesis because my regex fu is out to lunch.
                $version['raw'] = str_replace( '(' , '', $version['raw'] );
				$version['raw'] = str_replace( ')' , '', $version['raw'] );
                //break up string into version components
                //does the version have a date after the version number?
                if ( strstr( $version['raw'], '+' ) ) {
                    $ver_parts = explode('+', $version['raw']);
                    //handle if the text after '+' is a changeset, not a date
                    if ( date_parse( $ver_parts[1] ) ) {
                        $version['date'] = $ver_parts[1];
                    }
                    else{
                       $version['changeset'] = $ver_parts[1];
                    }
                }
                else {
                    $ver_parts[0] = $version['raw'];
                }

                $version['complete'] = $ver_parts[0];

                $version_tmp = explode('.', $ver_parts[0]);

                $version['major'] = $version_tmp[0];
                $version['minor'] = $version_tmp[1];

				//$this->_version = $version['raw'];

				return $version;
			}

			public function getExecutable()
			{
			    $paths = split( ':', $_ENV['PATH'] );
			    foreach ($paths as $a_path ) {
			        $able_to_use = is_executable( 'hg' );
			    }

				//@todo can I expand the path of the executable?
			}

			public function getRepository()
			{

			}
	}

?>