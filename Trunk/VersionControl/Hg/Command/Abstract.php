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
     * All possible options the command may receive.
     *
     * Its constructed by merging $valid_options, $optional_options,
     * and $required_options
     *
     * @var mixed
     */
    protected $valid_options = array();

    /**
     * Options which all commands may have.
     *
     * Child classes should not override this property, nor add elements to it.
     * I wish the final keyword could be applied to properties. I could,
     * actually make it private with a public accessor, but the 'final'
     * keyword would be so much cleaner.
     *
     * @var array
     */
    protected $global_options = array(
        'encoding' => null,
        'quiet' => null,
        'verbose' => null
    );

    /**
     * Non-required options this command may receive
     *
     * @var mixed
     */
    protected $optional_options = array();

    /**
     * Required options this command needs
     *
     * @var mixed
     */
    protected $required_options = array();

    /**
     * The current options applied to the Hg executable.
     *
     * @var mixed
     */
    protected $options = array();

    /**
     * Class constructors must be redefined in each Command parent class,
     * since it must have its dependencies for $container passed in.
     *
     * @return void
     */
    abstract function __construct($params);

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
                return $this->execute($arguments);
                    //interface demands all command classes define this method
                //alt: return call_user_func_array(array($command, 'execute'), $options);
                break;
            default:
            	/* must be the command or one of its fluent api functions */
                //is it a method of the currently instantiated command?
                if ( method_exists($this, $method) ) {
                    return call_user_func_array(array($this, $method), $arguments);
                } else {
                	throw new VersionControl_Hg_Command_Exception(
                	    "This method '{$method}' does not exist in this class"
                	);
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
     *
     * @return  VersionControl_Hg_Command
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
     *
     * @return  VersionControl_Hg_Command
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
     * Processes the options specified in client code and populates
     * $valid_options class member if it wasn't already set.
     *
     * @param array $options the options to set
     *
     * @return void
     * @throws void
     */
    protected function setOptions(array $options)
    {
    	if ( empty($this->valid_options) ) {
    	    $this->valid_options = array_merge(
                $this->allowed_options,
                $this->global_options,
                $this->required_options
            );
    	}

        /* $param[0] causes a Php Notice when its an empty array without this
         * topmost check
         */
        if ( count($options) > 0 ) {
        	/* redefine $options; 0th index because __call shunts all args
        	 * into an array.
        	 */
        	$options = $options[0];

            if ( is_array($options) ) {
                $keys = array_keys($options);
                /* reassign params so the values become string keys and
                 * replace the numeric values with nulls for options
                 */
                if ( is_numeric($keys) ) {
                    $options = array_flip($options);
                    foreach ( $options as $key => $value ) {
                        $options[$key] = null;
                    }
                }
                $this->addOptions($options);
            }
            elseif ( is_string($options) ) {
                //addOption() checks for validity
                $this->addOption($options, null);
            }
        }
    }

    /**
     * Formats the options to a string in CLI style: ' --option [ = value]'
     *
     * @param mixed $options the options in an array to format
     *
     * @return string the formatted options
     */
    protected function formatOptions(array $options)
    {
        $modifiers = null;
        /*
         * ensure all required options are defined
         */
        $missing_required_options =
            array_diff_key($this->required_options, $options);
        if ( count($missing_required_options) > 0 ) {
            throw new VersionControl_Hg_Command_Exception(
                'Required option(s) missing: ' .
                implode(', ', $missing_required_options)
            );
        }
        /* good, we have all required options, so let's format them */
        foreach ($options as $option => $argument) {
        	/*
        	 * this is why we have nulls as values for options which do not
        	 * have arguments. A better way later may be checking is_null()
        	 * and remove the extra space, but the Hg executable does not seem
        	 * to mind extra spaces in the command line.
        	 */
            $modifiers .= ' --' . $option . ' ' . $argument;
        }

        return $modifiers;
    }

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
        if ( ! array_key_exists($name, $this->valid_options) ) {
            throw new VersionControl_Hg_Command_Exception(
                "The option '{$name}' is not an valid option"
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
     * @return  boolean
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

        return true;
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
        $unset = false;

        if( array_key_exists($this->getOptions(), $option) ) {
            /* unset is a language construct and returns void, so no shortcuts
             * like if( unset(...) = array_key_exists(...) ) */
            unset( $this->_options[$option] );
            $unset = true;
        }

        return $unset;
    }

    /**
     * Parses the result of the Mercurial CLI operation into a semantic
     * associative array
     *
     * @todo refactor into Hg/Command/Result/Parser.php
     *
     * @param   mixed $output is the output to parse into an array of arrays
     * @param   mixed $fields array are the labels of columns;
     *
     * @return mixed
     */
    protected function parseOutput(array $output, $fields = null)
    {
        $parsed_output = array();

        foreach ( $output as $line ) {
	        /* split each line into columns by any type of space character
	         * repeated any number of times.
	         */
        	$bundle = preg_split('/\s/', $line);
            /* replace the numeric key with a field label
             * a list() idiom might be best here
             */
        	if ( ! is_null($fields) ) {
                //counts of field and output lengths must match.
                if ( count($fields) !== count($bundle) ) {
                    throw new VersionControl_Hg_Command_Exception(
                        'fields do not match the output'
                    );
                }

		        foreach ( $bundle as $key => $value ) {
                    unset($bundle[$key]);
                    $bundle[$fields[$key]] = $value;
		        }
        	}

            $parsed_output[] = $bundle;
                //'/[\s]+/' which is better?
        }

        return $parsed_output;
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
     * @return  VersionControl_Hg_Command
     */
    public function changeset($first, $last)
    {
        $this->addOption('rev', $first);
        /* let me be chainable! */
        return $this;
    }
}
