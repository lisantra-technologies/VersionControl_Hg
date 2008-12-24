<?php

	class Hg
	{
		/**
		 * The path of the repository.
		 * @var string
		 */
		private $_path;

		public function __construct() {}

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