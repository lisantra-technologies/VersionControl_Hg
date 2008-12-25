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