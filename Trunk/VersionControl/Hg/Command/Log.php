<?php
/**
 * Contains the definition of the Log command
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2011 Lisantra Technologies, LLC
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
 * Implements the log command.
 *
 * The hg command-line client also uses 'history' as an alias.
 *
 * Patching and diffing options are not available in this class. Please select
 * a file first and then request a diff for it:
 * <code>$hg->diff('git'|'gnu')->files(array('index.php', 'default.html'))->between('tip|2011-04-11')->revision(22)|->date('2011-03-01');</code>
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2011 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Log
    extends VersionControl_Hg_Command_Abstract
        implements VersionControl_Hg_Command_Interface
{
    /**
     * The name of the mercurial command implemented here
     *
     * @var string
     */
    protected $command = 'log';

    /**
     * Template which Mercurial uses to ouput data.
     *
     * We want a single-line output which is easy to parse!
     *
     * @var string
     */
    protected $template = '{rev}##{branch}##{files}##{date}##{author}##{desc}\r\n';

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
        'template' => null,
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
        'follow' => null,
        'at' => null,
        'on' => null,
        'between' => null,
        'files' => null, // implement with search_for() / searchFor / search
        'keyword' => null,
        'limit' => null,
        'branch' => null, //or should we force a branch selection first?
        'copies' => null, //show copied files
        'removed' => null, //show removed files
    );

    /**
     * Constructor
     *
     * @param array $param is one or more parameters to modify the command
     * @return void
     */
    public function __construct($params = null)
    {
        /* should always be called so we have a full array of valid options */
        $this->setOptions($params);

        $this->addOption('template', "$this->template");
    }

    /**
     * Execute the command and return the results.
     *
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute(array $params = null)
    {
        /* take care of options passed in as such:
         * $hg->status(array('revision' => 3, 'all' => null));
         * We need 'all' to be the key, and not have it interpreted as
         * 	revision => 3, 0 => all  */
        if ( ! empty($params) ) {
            $this->setOptions($params);
        }

        /* --noninteractive is required since issuing the command is
         * unattended by nature of using this package.
         * --repository PATH is required since the PWD on which hg is invoked
         * will not be within the working copy of the repo. */
        $this->addOptions(array(
            'noninteractive' => null,
            'repository' => $this->hg->getRepository()->getPath(),
        ));

        /* Despite its being so not variable, we need to set the command string
         * only after manually setting options and other command-specific data */
        $this->setCommandString();
//var_dump($this->command_string);
        /* no var assignment, since 2nd param holds output */
        exec($this->command_string, $this->output, $this->status);
//var_dump($this->output);
        if ( $this->status !== 0 ) {
            throw new VersionControl_Hg_Command_Exception(
                VersionControl_Hg_Command_Exception::COMMANDLINE_ERROR
            );
        }

        return $this->parseOutput(
            $this->output,
            array('rev', 'branch', 'file', 'datetime', 'author', 'description'),
            '##'
        );
    }

    /**
     * Adds 'rev' to the stack of command line options
     *
     * Specified the revision to restrict the log operation to

     * Usage:
     * <code>$hg->log('all')->revision(7)->run();</code>
     * or
     * <code>$hg->log(array('revision' => 7 ))->all()->run();</code>
     *
     * @param int|string $revision
     * @return void
     */
    public function revision($revision = 'tip') {
        //@TODO Technically, the following shouldn't occur since 'tip' is default
        if ( empty($revision)) {
            throw new VersionControl_Hg_Command_Exception(
                VersionControl_Hg_Command_Exception::BAD_ARGUMENT
            );
        }

        $this->addOption('rev', $revision);

        /* For the fluent API */
        return $this;
    }

    /**
     * Adds 'removed' to the stack of command line options
     *
     * Returns only files which have been removed from the working copy
     * and are no longer tracked by Mercurial.
     *
     * Usage:
     * <code>$hg->log()->removed()->run();</code>
     * or
     * <code>$hg->log('removed')->run();</code>
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
     * Adds 'copied' to the stack of command line options
     *
     * Returns only files copied within the working copy
     *
     * Usage:
     * <code>$hg->log()->copied()->run();</code>
     * or
     * <code>$hg->log('copied')->run();</code>
     *
     * @param null
     * @return null
     */
    public function copied() {
        $this->addOption('copied');
        return $this; //for the fluent API
    }

    /**
     * Adds a list of files to the stack of command line options
     *
     * List status information for only the files specified. Abstract::formatOptions
     * will automatically place this as the last option since a files list
     * must be the last item on the command line.
     *
     * Usage:
     * <code>$hg->log()->files(array('index.php'))->run();</code>
     * or
     * <code>$hg->log(array('files' => array('index.php')))->run();</code>
     *
     * @param mixed $files the list of files as a simple array
     * @return null
     *
     * @TODO how to ensure this is the final option??
     */
    public function files(array $files)
    {
        $this->addOption('files', join(' ', $files));
        return $this; //for the fluent API
    }
}
