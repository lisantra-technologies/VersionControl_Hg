<?php
    /**
     * Contains the Repository class.
     */


    /**
     * Represents the Repository as an entity.
     *
     * Usage:
     *
     * $m->repo()->load('/path/');
     * $m->repo()->create('/path/');
     *
     * $m = hg::init()->load('/path/');
     * $m = hg::init()->create('/path/');
     *
     *
     * @author Michael Gatto <mgatto@u.arizona.edu>
     * @package
     * @subpackage
     * @category
     * @license PHP
     */
    class Repository
    {
        /**
         * The name of all Mercurial repository roots.
         *
         * Leading backslash is needed since _path may not have a trailing slash.
         *
         * @const string
         */
        const REPO_ROOT_DIRECTORY_NAME = '/.hg';

        /**
         * Designates the repository as on the local filesystem.
         *
         * @const integer
         */
        const HG_LOCAL_REPOSITORY = 1;

        /**
         * Designates the repository as on a remote system.
         *
         * @const integer
         */
        const HG_REMOTE_REPOSITORY = 1;

        /**
         * alias for $_path?
         *
         * @var string
         */
        private $_uri;
        private $_remote_transport;
        private $_remote_username;
        private $_remote_password;

        /**
         * The file to be operated upon with relation to the repository.
         *
         * @var string
         */
        private $_file;

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
         * Create a new Mercurial repository.
         *
         * Implements `hg init`
         *
         * @param $uri the path for the repository to be created
         * @return boolean true on success, false on failure
         */
        public function create( $uri )
        {
            //call isRepository and throw an exception if a repo already exists here, or let `hg init` do it for me?

            //create a command object and run it. it should return an exception type if it borked.

            exec( 'hg init' . $options, $output );


            $this->setPath( $uri );
        }

        /**
         * Set an existing Mercurial repository
         *
         * @param $uri the path for the repository to be used in subsequent ops.
         * @return boolean true on success, false on failure
         */
        public function load( $uri ) {} //accepts file uris for now; load_from_url(); load_from_filesystem()

        public function delete() {}
        public function move_to() {}
        public function rename_to() {}
        public function clone_from()
        {
            //call load()
            //call create()
        }

        private function construct_url()
        {
            //require PEAR/Net/URL2.php
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

        /**
         * Set the specific file within the repository (or, working copy??) to operated upon
         *
         * @return Repository
         * @see $_file
         */
        public function getFile( $file )
        {
            //should raise exception if the file is not versioncontrolled?
            //@todo How to find out? Run Status.php and ensure $file is in_array()?

            $this->_file = $file; //will be overwritten each time function is called.
            return $this;
        }

        public function __call( $function, $options )
        {
            if( class_exists( $function ) ) {
                $cmd = new $function( $this, $options );
            } else {
                throw new Exception('Sorry, The command \'{$function}\' is not implemented.');
            }
        }

        //this is a temporary alias for __call...
        public function command() {}

    }

?>