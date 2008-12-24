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
		 * @return string
		 * @see $_path
		 */
		public function getPath()
			{
				return (string) $this->_path;
			}

		/**
		 *
		 * @param string $path
		 * @return void
		 * @see $_path
		 * @assert is_dir( $path ) === true
		 */
		public function setPath( $path )
			{
				//@todo add checking the directory
				$this->_path = $path;

				//@todo return $this so we can chain the methods.
				return true;
			}
	}

?>