<?php


    /**
     * Wraps each Mercurial command to centralize global options and command execution.
     *
     * It is passed into the constructor of each command.
     *
     * implements the following global options:
     * -I --include    include names matching the given patterns
     * -X --exclude    exclude names matching the given patterns
     *
     * @package VersionControl_Hg
     * @subpackage Commands
     */
    class Hg_Repository_Command extends Hg_Repository
    {
        /**
         * Holds self:: to pass to classes implementing Mercurial commands.
         *
         * @var Command
         */
        private $_command;

        /**
         * Hold the repository object that commands will act upon.
         *
         * @var Repository
         */
        private $_repo;

        /**
         * Success or failure of the command.
         *
         * @var boolean
         */
        private $_result;

        /**
         * Hold the template for the output of Mercurial commands.
         *
         * @var string
         */
        private $_template;

        /**
         * Holds returned data from the Mercurial shell command.
         *
         * @var array
         */
        private $_output;

        /**
         * Manages the options for the Hg executable.
         *
         * @var array
         */
        private $_options = array();

        /**
         * Manages list of data entries, correpsonding to the output of Mercurial commands
         * usually in the form of lists of files and directories with thier versioning attributes.
         *
         * @var Collection
         */
        private $_collection;

        public function __construct( Repository $repo )
        {
            $this->_repo = $repo;
            //this is a non constructed class, so we "privatize" the constructor to prevent construction.
        }

        //executes the actual mercurial command
        protected function _execute( $command, array $options )
        {
            //process options array; everything should be in long format.
            // the leading space in the join param: ' --' is essential.
            //as is the blank space between hg and $command
            $this->_result = exec( 'hg' . ' ' . $command . join( ' --', $this->getOptions() ), $this->_output );
        }

        /**
         *
         * @param
         * @param $fields array holds the labels of the fields
         * @return
         */
        protected function parseOutput( array $fields, array $commandOutput )
        {
            $output = array();

            /*
             * preg_split returns an array.
             * Regex accounts for the different line endings on the 3 platforms: Win32, Mac and *nix.
             */
            $lines = preg_split( '/\r\n|\r|\n/', $fixture );

            //split each line into columns by any type of space charachter repeated any number of times.
            foreach( $lines as $a_line ) {
               $output[] = preg_split( '/[\s]+/', $a_line );
            }

            //list() idiom might be best here
            foreach ( $output as $row_num => $row ) {
                //counts of field and output lengths must match.
                $field_length = count( $fields );
                $output_row_length = count( $row );

                //loop through the variable-length output row.
                foreach ( $row as $position => $value ) {
                    $result[$row_num][$fields[$position]] = $value;
                }
            }

            return $output;
        }


        /**
         *
         * @param $style defaults to xml
         * @return Command
         */
        public function setTemplate( $template = 'xml' )
        {
            $this->_template = 'xml';
            return $this;
        }

        public function getTemplate()
        {
            return $this->_template;
        }

        /**
         *
         * @param $template string
         * @return unknown_type
         */
        private function formatWithTemplate( $template = 'xml' )
        {
            if ( $template == 'xml') {
                //@todo set the style for xml.
                $templateFormat = '';
            }

            $this->setOption( 'template', $templateFormat );
        }


    /*
     * Handle Command Options
     */
        /**
         *
         * @param $name is the name of the option which Mercurial recognizes
         * @param $value is optional since not all Hg options need a value
         * @return boolean
         */
        protected function setOption( $name, $value = NULL )
        {
            //really simplistic, but hey KISS and then refactor!
            if( $this->_options[$name] = $value ) {
                 $status = true;
            } else {
                $status = false;
            }

            return $status;
        }


        public function getOptions()
        {
            return $this->_options;
        }

        public function unsetOption( $option )
        {
            if( array_key_exists( $this->getOptions(), $option ) ) {
                unset( $this->_options[$option] );
                $status = true;
            } else {
                $status = false;
            }

            return $status;
        }

    /*
     * API functions which handle named parameters.
     */
        /**
         *
         * @param $filter string
         * @return Command
         */
        public function excluding( $filter )
        {
            /*
             * Mercurial expects the pattern to start with 'glob: ' or 're: '.
             */
            $pattern = 'glob: '. escapeshellarg( $filter );

            $this->setOption( 'exclude', $pattern );

            /*
             * let me be chainable!
             */
            return $this;
        }

        /**
         *
         * @param $filter string
         * @return Command
         */
        public function including( $filter )
        {
            /*
             * Mercurial expects the pattern to start with 'glob: ' or 're: '.
             */
            $pattern = 'glob: '. escapeshellarg( $filter );

            $this->setOption( 'include', $pattern );

            /*
             * let me be chainable!
             */
            return $this;
        }

        //revisions are considered inclusive: r1 to r3 includes data from r1,r2,r3.
        public function changeset( $first, $last )
        {
            $this->setOption( 'rev', $first );
        }

        /**
         * Implements the  option for the Mercurial executable.
         *
         * @param $boolean
         * @return Command
         */
        public function verbose( $boolean )
        {
            if( $boolean == true ) {
               $this->setOption( 'rev', $first );
            } elseif( $boolean == false ) {
                $this->unsetOption( 'rev' );
            }

            /*
             * let me be chainable!
             */
            return $this;
        }

        /*
         * alias for changeset
           public function _revision( $first, $last ) {}
        */

        abstract function _getStatus();
        abstract function _setStatus();
        abstract function _getError(); //parseError
        abstract function _getOutput(); //return an array of data


        //output['type'] is 'error|data'
        //output['data'] is a file collection

        //private function addToCollection( );
        //private function removeFromCollection( $typeOfCollection, $filter );

    }

?>