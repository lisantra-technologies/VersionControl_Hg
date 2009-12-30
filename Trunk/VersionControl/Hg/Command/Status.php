<?php
/**
 * Contains the definition of the Status command
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     SVN:
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides the required interface for all commands
 */
require_once 'Interface.php';

/**
 * Provides base functionality common to all commands
 */
require_once 'Abstract.php';

/**
 * Provides Exceptions for commands (VersionControl_Hg_Command_Exception)
 */
require_once 'Exception.php';

/**
 * Implements the status command.
 *
 * The codes used to show the status of files are:
 *  M = modified
 *  A = added
 *  R = removed
 *  C = clean
 *  ! = missing (deleted by non-hg command, but still tracked)
 *  ? = not tracked
 *  I = ignored
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Release: 0.3.0
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Status
    extends VersionControl_Hg_Command_Abstract
    implements VersionControl_Hg_Command_Interface
{
    /**
     * The name of the mercurial command implemented here
     *
     * @var string
     */
    protected $command = 'status';

    /**
     * Required options for this specific command. These may not be required
     * by Mercurial itself, but are required for the proper functioning of
     * this package.
     *
     * @var mixed
     */
    protected $required_options = array(
        'noninteractive' => null,
        'repository' => null,
    );

    protected $allowed_options = array(
        'all' => null,
        'modified' => null,
        'added' => null,
        'removed' => null,
        'deleted' => null,
        'clean' => null,
        'unknown' => null,
        'ignored' => null,
    );

    /**
     * Constructor
     *
     * @param array $param is one or more parameters to modify the command
     *
     * @return void
     */
    public function __construct($param = null)
    {
    	$this->allowed_options = array_merge(
    	   $this->allowed_options,
    	   $this->global_options,
    	   $this->required_options
    	);
    	/*
    	 * $param[0] causes a Php Notice when its an empty array without this
    	 * topmost check
    	 */
    	if ( (! is_null($param) ) && ( count($param) > 0 ) ) {
	    	if ( is_array($param[0]) ) {
	    		$keys = array_keys($param[0]);
	    		if ( is_numeric($keys[0]) ) {
	    			//reassign params so the values become string keys
                    $param[0] = array_flip($param[0]);//replace the numeric values with nulls for options
                    foreach ( $param[0] as $key => $value ) {
                        $param[0][$key] = null;
                    }
	    		}
	            $this->addOptions($param[0]);
	        }
	        elseif ( is_string($param[0]) ) {
	        	//addOption() checks for validity
	            $this->addOption($param[0], null);
	        }
        }
    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute(array $options)
    {
        //this $options[0] thingy is because __call makes the arguments into an array
        if ( is_array($options[0])) {
            $this->addOptions($options[0]);
        }
        elseif ( is_string($options[0]) ) {
            $this->addOption($options[0]);
        }
        /* set the required options */
        $this->addOptions(array(
            'noninteractive' => null,
            'repository' => $this->container->getRepository()->getPath(),
        ));

        $modifiers = null;
        $options = $this->getOptions();
        $missing_required_options = array_diff_key($this->required_options, $options);
        if ( count($missing_required_options) > 0 ) {
        	throw new VersionControl_Hg_Command_Exception(
        	    'Required option(s) missing: ' .
        	    implode(', ', $missing_required_options)
            );
        }

        foreach ($options as $option => $argument) {

            $modifiers .= ' --' . $option . ' ' . $argument;
        }

        //$command_string = escapeshellcmd();
        $command_string = '"' . $this->container->getExecutable()->getExecutable() . '" ' . $this->command;
        $command_string .= rtrim($modifiers);

        exec($command_string, $output, $command_status);

        //@todo remove the die()...
        ($command_status === 0) or die("returned an error: $command_string");

        $status = array();

        foreach ( $output as $line ) {
        	array_push($status, preg_split('/\s/', $line));
        }
        array_push($status, $command_string);

        $this->status = $command_status;
        $this->output = $output;

        return $status;
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function all()
    {
        $this->addOption('all', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'modified' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->modified();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('modified')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function modified()
    {
        $this->addOption('modified', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function added()
    {
        $this->addOption('added', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function removed()
    {
        $this->addOption('removed', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function deleted()
    {
        $this->addOption('deleted', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function clean()
    {
        $this->addOption('clean', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function unknown()
    {
        $this->addOption('unknown', null);
        return $this; //for the fluent API
    }

    /**
     * Sets the 'all' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     *
     * An alternative to this style:
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     *
     * @return null
     */
    public function ignored()
    {
        $this->addOption('ignored', null);
        return $this; //for the fluent API
    }

    /**
     * Print out the class' properties
     *
     * @return string
     */
    public function __toString()
    {
    	var_dump($this->output);
    }
}
