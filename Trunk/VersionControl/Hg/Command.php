<?php

/**
 * Contains the class definition for VersionControl_Hg_Command
 *
 * PHP version 5
 *
 * @category VersionControl
 * @package Hg
 * @author Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Wraps each Mercurial command to centralize global options and command execution.
 *
 * It is passed into the constructor of each command.
 *
 * implements the following global options:
 * -I --include    include names matching the given patterns
 * -X --exclude    exclude names matching the given patterns
 *
 * PHP version 5
 *
 * @category VersionControl
 * @package Hg
 * @author Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command
{
    /**
     * Holds self:: to pass to classes implementing Mercurial commands.
     *
     * @var Command
     */
    private $_command;

    /**
     * Implemented commands pertaining to the Hg executable.
     * @var unknown_type
     */
    private $_allowed_commands = array(
        'version',
    );

    protected $valid_options = array(
        'encoding',
        'quiet',
        'verbose',
    );

    protected $required_options = array();

    /**
     * Object representing the Hg executable
     *
     * @var VersionControl_Hg
     */
    protected $hg;

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
    protected $options = array();

    /**
     * Manages list of data entries, correpsonding to the output of Mercurial commands
     * usually in the form of lists of files and directories with thier versioning attributes.
     *
     * @var Collection
     */
    private $_collection;

    public function __construct(VersionControl_Hg $hg)
    {
        $this->hg = $hg; //already instantiated; passed when a command is called by Hg.php
    }

    final protected function setCommand($command, $options)
    {


    }

    public function __call($method, $args)
    {
        if ( ! in_array($method, $this->_allowed_commands)) {
            throw new VersionControl_Hg_Command_Exception(
                'Command not implemented or not a part of Mercurial'
            );
        }

        $class = 'VersionControl_Hg_Command_' . ucfirst($method);

        include_once "Command/" . ucfirst($method) . ".php";

        if (class_exists($class)) {
            $options = $args[0];

            $command_class = new $class($this->hg);
            return $command_class->execute($options);
        }
        //$command, array $options = null

        //instantiate the class implementing the command

        //run its execute method
    }


    //executes the actual mercurial command
    protected function prepareCommand()
    {
        //might need to format the command per OS: double quotes, etc...
        //$command_string = "\"{$this->getHgExecutable()}\" "
    }

    /**
     *
     * @param
     * @param $fields array holds the labels of the fields
     * @return
     */
    protected function parseOutput(array $fields, array $commandOutput)
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
    public function setTemplate($template = 'xml')
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
    private function formatWithTemplate ($template = 'xml')
    {
        if ( $template == 'xml') {
            //@todo set the style for xml.
            $templateFormat = '';
        }

        $this->addOption('template', $templateFormat);
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
    protected function addOption($name, $value = NULL)
    {
        //really simplistic, but hey KISS and then refactor!
        if( $this->_options[$name] = $value ) {
             $status = true;
        } else {
            $status = false;
        }

        return $status;
    }

    protected function addOptions(array $options)
    {
        if ( ! is_array($options)) {
            throw new VersionControl_Hg_Command_Exception(
                'Options is not an array'
            );
        }

        foreach ($options as $name => $value) {
            $this->valid_options[$name] = $value;
        }
    }

    public function getOptions()
    {
        //@todo check that defined options satisfy $required_options

        return $this->options;
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
    public function excluding($filter)
    {
        /*
         * Mercurial expects the pattern to start with 'glob: ' or 're: '.
         */
        $pattern = 'glob: '. escapeshellarg($filter);

        $this->addOption('exclude', $pattern);

        /*
         * let me be chainable!
         */
        return $this;
    }

    public function forFiles() {}
    public function forDir() {}
    public function from() {}
    public function to() {}


    /**
     *
     * @param $filter string
     * @return Command
     */
    public function including($filter)
    {
        /*
         * Mercurial expects the pattern to start with 'glob: ' or 're: '.
         */
        $pattern = 'glob: '. escapeshellarg($filter);

        $this->addOption('include', $pattern);

        /*
         * let me be chainable!
         */
        return $this;
    }

    //revisions are considered inclusive: r1 to r3 includes data from r1,r2,r3.
    public function changeset( $first, $last )
    {
        $this->addOption( 'rev', $first );
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
/*
    abstract function _getStatus();
    abstract function _setStatus();
    abstract function _getError(); //parseError
    abstract function _getOutput(); //return an array of data
*/

    //output['type'] is 'error|data'
    //output['data'] is a file collection

    //private function addToCollection( );
    //private function removeFromCollection( $typeOfCollection, $filter );

}
