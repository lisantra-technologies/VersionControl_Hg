<?php
	/**
	 * @package VersionControl_Hg
	 * @license
	 * @copyright
	 * @version $Revision$
	 */

	/**
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
		 *
		 * @return
		 */
		public function __construct()
			{

			}

		/**
		 *
		 * @param string $path
		 * @return void
		 * @see $_path
		 * @assert ( $path ) === true
		 */
		public function setPath( $path )
			{
				//@todo add checking the directory

				//ensure there is a string passed to me.
				if ( ! empty( $path ) ) {
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
				return $this->_path;
			}
	}

?>