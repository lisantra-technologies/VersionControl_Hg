<?php
/**
 * Contains definition for the Abstract class inherited by all command classes
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link 		http://pear.php.net/package/VersionControl_Hg
 */

/**
 *  Include custom exception object
 */
require_once 'Exception.php';

/**
 *
 *
 * implements the following global options:
 * -I --include    include names matching the given patterns
 * -X --exclude    exclude names matching the given patterns
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link 		http://pear.php.net/package/VersionControl_Hg
 */
abstract class VersionControl_Hg_Command_Abstract
{
    /**
     * Success or failure of the command.
     *
     * @var boolean
     */
    protected $status;

    /**
     * Holds returned data from the Mercurial shell command.
     *
     * @var array
     */
    protected $output;

    /**
     * Object representing the container the command operated upon
     *
     * @var VersionControl_Hg
     */
    protected $container;

    /**
     * The type of object the command operates on: 'hg', 'repository'
     * or 'working_copy'
     *
     * @var string
     */
    public $operates_on;

    /**
     * Options which all commands in the package may have.
     *
     * Child classes shoud not override this property, nor add elements to it.
     * I wish the final keyword could be applied to properties. I could,
     * actually make it private with a public accessor, but the 'final'
     * keyword would be so much cleaner.
     *
     * @var mixed
     */
    protected $global_options = array(
        'encoding' => null,
        'quiet' => null,
        'verbose' => null
    );

    /**
     * Possible options this command may have
     *
     * @var mixed
     */
    protected $allowed_options = array();

    /**
     * Required options this command needs
     *
     * @var mixed
     */
    protected $required_options = array();

    /**
     * Manages the options for the Hg executable.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Class constructors must be redefined in each Command parent class,
     * since it must have its dependencies for $container passed in.
     *
     * @return void
     */
    abstract function __construct($param);

/**
     * Executes the actual mercurial command
     *
     * For example, the programmer writes <code>$hg->archive('tip');</code>.
     * 'archive' and its parameter 'tip' are passed by a series of __call()
     * invocations. 'archive' is used to identify the class which implements
     * the command, while its parameter will be used in the constructor. We
     * have to be a little rigid and say that archive()'s only parameter can
     * be the revision we want to archive.
     *
     * `run()` is used to trigger execution. But, it is a virtual function:
     * i.e. it will always be intercepted by __call in
     * VersionControl_Hg_Repository_Command.
     *
     * @param   string $method
     * @param   mixed $args
     *
     * @return  mixed
     * @throws  VersionControl_Hg_Command_Exception
     */
    public function __call($method, $arguments) {
        /* $arguments is an array which may be empty if $hg->command() [->run()]
           has no parameters */
        switch ($method) {
            case 'run': //the special method ending the fluent chain
                /* run the command class' execute method */
                return $this->execute($arguments); //interface demands all command classes define this method
                //alt: return call_user_func_array(array($command, 'execute'), $options);
                break;
            default:
            	//it must be one of the methods custom-defined for the command
                //is it a method of the currently instantiated command implementor?
                if ( method_exists($this, $method) ) {
                    return call_user_func_array(array($this, $method), $arguments);
                } else {
                	throw new VersionControl_Hg_Command_Exception();
                }
        }
    }

    /**
     * Exclude files and / or directories from consideration.
     *
     * This option is available for commands which operate on both working
     * copies and repositories, thus its abstraction.
     *
     * Mercurial expects the pattern to start with 'glob: ' or 're: '.
     *
     * @todo refactor out to Hg/Command/Filter/Excluding.php
     *
     * @param   $filter string
     * @return  Command
     */
    public function excluding($filter)
    {
        $this->addOption(
            'exclude', 'glob: '. escapeshellarg($filter)
        );

        /* let me be chainable! */
        return $this;
    }

    /**
     * Include files and / or directories from consideration
     *
     * This option is available for commands which operate on both working
     * copies and repositories, thus its abstraction.
     *
     * Mercurial expects the pattern to start with 'glob: ' or 're: '.
     *
     * @todo refactor out to Hg/Command/Filter/Including.php
     *
     * @param   $filter string
     * @return  Command
     */
    public function including($filter)
    {
        $this->addOption(
            'include', 'glob: '. escapeshellarg($filter)
        );

        /* let me be chainable! */
        return $this;
    }

    //@todo consider refactoring into its own Option class?

    /**
     * Add an option
     *
     * @param   $name is the name of the option which Mercurial recognizes
     * @param   $value is optional since not all Hg options need a value
     *
     * @return  boolean
     * @throws  VersionControl_Hg_Command_Exception
     */
    protected function addOption($name, $value = NULL)
    {
        if ( ! array_key_exists($name, $this->allowed_options) ) {
            throw new VersionControl_Hg_Command_Exception(
                "The option '{$name}' is not an allowed option"
            );
        }

        $this->options[$name] = $value;
        //this will always return true unless it throws an exception, so
        //why return anything at all?
        return true;
    }

    /**
     * Add a set of options all at once
     *
     * @param   array $options
     *
     * @return  unknown_type
     */
    protected function addOptions(array $options)
    {
        if ( ! is_array($options)) {
            throw new VersionControl_Hg_Command_Exception(
                'Options is not an array'
            );
        } //@todo is this necessary since array is hinted in the signature?

        foreach ($options as $name => $value) {
            $this->addOption($name, $value);
        }
    }

    /**
     * Return all the options currently defined for the command
     *
     * @return mixed
     */
    public function getOptions()
    {
        //@todo check that defined options satisfy $required_options

        return $this->options;
    }

    /**
     * Remove an option from the command by its name
     *
     * @param string $option
     *
     * @return boolean is false when $option does not exist in the options array
     */
    public function unsetOption($option)
    {
        $status = false;

        if( array_key_exists($this->getOptions(), $option) ) {
            /* unset is a language construct and returns void, so no shortcuts
             * like if( unset(...) = array_key_exists(...) ) */
            unset( $this->_options[$option] );
            $status = true;
        }

        return $status;
    }

    /**
     *
     * @todo refactor into Hg/Command/Result/Parser.php
     *
     * @param   mixed $commandOutput
     * @param   mixed $fields array holds the labels of the fields
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
     * Sets the container in which  the command operates
     *
     * @param $container VersionControl_Hg
     */
    public function setContainer($container) {
    	$this->container = $container;
    }

    /**
     * Returns the container in which  the command operates
     *
     * @return VersionControl_Hg
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Specify which revisions to operate upon in the repository
     *
     * revisions are considered inclusive: r1 to r3 includes data
     * from r1,r2,r3.
     *
     * @param   $first
     * @param   $last
     *
     * @return  Command
     */
    public function changeset($first, $last)
    {
        $this->addOption('rev', $first);
        /* let me be chainable! */
        return $this;
    }
}
