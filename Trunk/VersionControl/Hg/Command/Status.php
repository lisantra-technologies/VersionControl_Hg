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

    /**
     * Permissable options.
     *
     * The actual option must be the key, while 'null' is a value here to
     * accommodate the current implementation of setting options.
     *
     * @var mixed
     */
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
    public function __construct($params = null)
    {
    	if ( ! is_null($params) ) {
            $this->setOptions($params);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute($params = null)
    {
        if ( ! is_null($params) ) {
            $this->setOptions($params);
        }

        /* set the required options */
        $this->addOptions(array(
            'noninteractive' => null,
            'repository' => $this->container->getRepository()->getPath(),
        ));

        //@todo use this: $command_string = escapeshellcmd() but it causes
        //problems on windows...
        $command_string =
            '"' .
            $this->container->getExecutable()->getExecutable() .
            '" ' . $this->command .
            rtrim($this->formatOptions($this->getOptions()));

        exec($command_string, $output, $status);

        //@todo remove the die()...
        ($status === 0) or die("returned an error: $command_string");

        /* set the class properties for possible future use... */
        $this->status = $status;
        $this->output = $output;

        $parsed_output = $this->parseOutput(
            $output,
            array('status', 'file')
        );

        //@todo remove after testing!
        array_push($parsed_output, $command_string);

        return $parsed_output;
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
