<?php
    /**
     * Contains the Repository class.
     *
     * @category VersionControl
     * @package Hg
     * @subpackage Repository
     * @author Michael Gatto <mgatto@u.arizona.edu>
     * @license PHP
     */

    /**
     * Represents the Repository as an entity.
     *
     * Usage:
     *
     * $repo = new Repository();
     * $repo->load( '/path/' )
     *
     * As a child of Hg.php:
     * $hg = new Hg();
     * $repo = $hg->getRepository( '/path/' );
     *
     * Or:
     * $repo = hg::instance()->load('/path/');
     * $repo = hg::instance()->create('/path/');
     *
     * @category VersionControl
     * @package Hg
     * @subpackage Repository
     * @author Michael Gatto <mgatto@u.arizona.edu>
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
        const HG_ROOT_DIRECTORY_NAME = '/.hg';

        /**
         * Holds the filesystem path of a Mercurial repository.
         *
         * @var string
         */
        private $_path;

        /**
         * Holds the current command operating on the repository.
         *
         * @var string
         */
        private $_command;

        /**
         * Repository constructor which currently does nothing.
         *
         * @todo might be a good place to set the transport method?
         */
        public function __construct() { }

        /**
         * Sets the path of a Mercurial repository after validating it as a Hg repo.
         *
         * @param string $path as a local filesystem path.
         * @return Repository to enable method chaining
         * @see $_path
         */
        public function setPath( $path )
        {
            $repo = $path . self::HG_ROOT_DIRECTORY_NAME;

            if ( ! $this->isRepository( $repo ) ) {
                throw new Exception( 'This path is not a valid Mercurial repository' );
            }

            $this->_path = $repo;
            return this; //for chainable methods.

        }

        /**
         * Returns the path of a Mercurial repository as set by the user.
         * It is validated before being set as a class member.
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

        /**
         * Checks if $this is in fact a valid
         *
         * @return boolean
         */
        public function isRepository()
        {
            /*
             * both conditions must be satisfied.
             */
            if ( ! ( is_dir( $repo ) && empty( $repo ) ) ) {
                return true;
            }
            else {
                return false;
            }
        }

        /**
         * Set an existing Mercurial repository to use
         *
         * Usage:
         *  $repo = new Repository();
         *  $repo->load( '/path/' );
         *  Or:
         *  $repo->setPath( '/path/' )->load();
         *
         * @todo change to accept a path on its own or getPath when chained
         *
         * @param $uri the path for the repository to be used in subsequent ops.
         * @return boolean true on success, false on failure
         */
        public function load( $path )
        {
            /*
             * Remember that the owner of the directory has to be the same of the script user,
             * otherwise this function will always return false when PHP is running in safe_mode..
             */
            $this->setPath( $path );

            /*
             * Let's not guess that the user wants to create a repo if none exists;
             * Throw and exception and let them decide what to do next.
             * Maybe they just gave the wrong path.
             */
            if ( ! $this->isRepository( $this->getPath() ) ) {

                throw new Exception( 'there is no Mercurial repository at: '
                    . $this->getPath()
                    . ' Use $hg->createRepository( \'/path/\' ) to create one and then use getRepository() to act upon it.' );
            }

            return $this;
        }

        /**
         * Create a new Mercurial repository.
         *
         * Implements `hg init`
         *
         * @param $uri the path for the repository to be created
         * @return boolean true on success, false on failure
         */
        public function create( $path )
        {
            //call isRepository and throw an exception if a repo already exists here, or let `hg init` do it for me?

            //create a command object and run it. it should return an exception type if it borked.

            if ( ! $this->getPath() ) {

            }

            $this->_command = new Init( $path );

        }

        /**
         * Deletes the repository, effectively removing the working copy from SCM.
         *
         * @return boolean
         */
        public function delete()
        {
            if ( unlink(  ) ) {
                return true;
            } else {
                return false;
                //throw new Exception( 'The repository could not be deleted.' );
            }

        }

        /**
         *
         * @param $function
         * @param $options
         * @return unknown_type
         */
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