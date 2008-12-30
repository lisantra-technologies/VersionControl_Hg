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
	 * @author Michael Gatto<mgatto@u.arizona.edu>
	 *
	 */
	class Hg
	{
		/**
		 * The version of the Mercurial executable.
		 * @var float
		 */
		private $_version;

		/**
		 * The path of the repository.
		 * @var string
		 */
		private $_path;

		/**
		 * Leading backslash is needed since _path may not have a trailing slash.
		 *
		 * @var string
		 */
		private $_repoRootDirectoryName = '/.hg';

		/**
		 *
		 * @return
		 */
		public function __construct( $path )
			{
				/*
				 * Remember that the owner of the directory has to be the same of the script user,
				 * otherwise this function will always return false when PHP is running in safe_mode..
				 */
				$this->setPath( $path );

				if ( ! $this->isRepository( $this->getPath() ) ) {
					throw new Exception( 'there is no Mercurial repository at: ' . $this->getPath() );
				}
			}

		/**
		 * Returns the version of the Mercurial executable.
		 * Implements the --version switch of the command-line client.
		 *
		 * @return array
		 * @see $_version
		 */
		public function getVersion( $version_data = NULL )
			{
			    if ( $version_data === NULL ) {
				    exec( 'hg --version', $output );
				    $ver_string = $output[0];
			    } else {
			        $ver_string = $version_data;
			    }

				//(version 1.1)
				//(version 1.1+20081220)
				//(version 1.1+e54ac289bed)
				//(unknown)

                /*
                 * handle bad input
                 */
                if ( preg_match( '/\(.*\)/', $ver_string, $ver_match ) == 0 ) {
                    throw new Exception('Unrecognized version data');
                }

                //this is only processed if no exception.
                $_version['raw'] = trim( substr( $ver_match[0], 8, strlen( $ver_match[0] ) ) );
                //replace the parenthesis because my regex fu is out to lunch.
                $_version['raw'] = str_replace( '(' , '', $_version['raw'] );
				$_version['raw'] = str_replace( ')' , '', $_version['raw'] );
                //break up string into version components
                //does the version have a date after the version number?
                if ( strstr( $_version['raw'], '+' ) ) {
                    $ver_parts = explode('+', $_version['raw']);
                    //handle if the text after '+' is a changeset, not a date
                    if ( date_parse( $ver_parts[1] ) ) {
                        $_version['date'] = $ver_parts[1];
                    }
                    else{
                       $_version['changeset'] = $ver_parts[1];
                    }
                }
                else {
                    $ver_parts[0] = $version['raw'];
                }

                $_version['complete'] = $ver_parts[0];

                $version_tmp = explode('.', $ver_parts[0]);

                $_version['major'] = $version_tmp[0];
                $_version['minor'] = $version_tmp[1];

				return $_version;
			}

		/**
		 *
		 * @param string $path
		 * @see $_path
		 * @assert ( $path ) === true
		 */
		public function setPath( $path )
			{
				//@todo add checking the directory

				//I don't want empty paths
				if ( ! empty( $path ) && is_dir( $path ) ) {
					$this->_path = $path;
					return true;
				}
				else {
					throw new Exception( 'A path is required' );
				}

				//@todo return $this so we can chain the methods.
			}

		/**
		 *
		 * @return string
		 * @see $_path
		 */
		public function getPath()
			{
				if ( ! empty( $this->_path ) ) {
					return $this->_path;
				}
				else {
					throw new Exception( 'There is no path to return' );
				}
			}

		public function isRepository()
			{
				if ( is_dir( $this->getPath() . $this->_repoRootDirectoryName ) ) {
					return true;
				}
				else {
					return false;
				}
			}


	}

?>