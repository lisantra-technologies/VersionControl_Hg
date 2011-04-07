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
 * @link        http://pear.php.net/package/VersionControl_Hg
 *
 * @TODO implement -C / --copied options
 * @TODO implement ability to show only particular files' status
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
        'unknown' => null, //could be 'not tracked'? but we need one word
        'ignored' => null,
    );

    /**
     * Mapping between native Hg output codes and human readable outputs.
     *
     * @var mixed
     * @TODO add optional functionality for this to parent::parseOutput()
     */
    protected $output_codes = array(
        'M' => 'modified',
        'A' => 'added',
        'R' => 'removed',
        'C' => 'clean',
        '!' => 'missing',
        //should be unknown here to match above, but HG docs use 'not tracked'
        '?' => 'not tracked',
        'I' => 'ignored',
    );

    /**
     * Constructor
     *
     * @param array $param is one or more parameters to modify the command
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
    public function execute(array $params = null)
    {
        if ( ! is_null($params) ) {
            $this->setOptions($params);
        }

        /*
         * --noninteractive is required since issuing the command is
         * unattended by nature of using this package.
         * --repository PATH is required since the PWD on which hg is invoked
         * will not be within the working copy of the repo.
         */
        //@TODO move this, if possible, to Abstract.php since it seems required for all commands
        $this->addOptions(array(
            'noninteractive' => null,
            'repository' => $this->container->getRepository()->getPath(),
        ));

        /* Despite its being so not variable, we need to set the command string
         * only after manually setting options and other command-specific data */
        $this->setCommandString();

        /* no var assignment, since 2nd param holds output */
        exec($this->command_string, $this->output, $this->status);

        if ( $this->status === 0 ) {
            throw new VersionControl_Hg_Command_Exception(
                VersionControl_Hg_Command_Exception::COMMANDLINE_ERROR
            );
        }

        return $this->parseOutput($this->output, array('status', 'file'));
    }

    /**
     * Sets the 'all' option
     *
     * Returns all files in the repository no matter their status.
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     * or
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     * @return null
     */
    public function all()
    {
        $this->addOption('all');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'modified' option
     *
     * Returns only files which have been modified in the working copy.
     *
     * Usage:
     * <code>$hg->status()->modified();</code>
     * or
     * <code>$hg->status('modified')->run();</code>
     *
     * @param null
     * @return null
     */
    public function modified()
    {
        $this->addOption('modified');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'added' option
     *
     * Returns only files newly added to the repository.
     *
     * Usage:
     * <code>$hg->status()->all()->run();</code>
     * or
     * <code>$hg->status('all')->run();</code>
     *
     * @param null
     * @return null
     */
    public function added()
    {
        $this->addOption('added');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'removed' option
     *
     * Returns only files which have been removed from the working copy
     * and are no longer tracked by Mercurial.
     *
     * Usage:
     * <code>$hg->status()->removed()->run();</code>
     * or
     * <code>$hg->status('removed')->run();</code>
     *
     * @param null
     * @return null
     */
    public function removed()
    {
        $this->addOption('removed');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'deleted' option
     *
     * Returns all files which have been deleted from the working copy.
     *
     * Usage:
     * <code>$hg->status()->deleted()->run();</code>
     * or
     * <code>$hg->status('deleted')->run();</code>
     *
     * @param null
     * @return null
     */
    public function deleted()
    {
        $this->addOption('deleted');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'clean' option
     *
     * Returns files which have no changes; i.e. they are identical in both
     * the repository and working copy.
     *
     * Usage:
     * <code>$hg->status()->clean()->run();</code>
     * or
     * <code>$hg->status('clean')->run();</code>
     *
     * @param null
     * @return null
     */
    public function clean()
    {
        $this->addOption('clean');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'unknown' option
     *
     * Returns all files not being tracked by Mercurial.
     *
     * Usage:
     * <code>$hg->status()->unknown()->run();</code>
     * or
     * <code>$hg->status('unknown')->run();</code>
     *
     * @param null
     * @return null
     */
    public function unknown()
    {
        $this->addOption('unknown');
        return $this; //for the fluent API
    }

    /**
     * Sets the 'ignored' option as part of the fluent API
     *
     * Usage:
     * <code>$hg->status()->ignored()->run();</code>
     * or
     * <code>$hg->status('ignored')->run();</code>
     *
     * @param null
     * @return null
     */
    public function ignored()
    {
        $this->addOption('ignored');
        return $this; //for the fluent API
    }

    /**
     * Print out the class' properties
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output;
    }
}
