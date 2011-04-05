<?php
/**
 * Contains the class which interacts with the system's `hg` program.
 *
 * PHP version 5
 *
 * @category  VersionControl
 * @package   Hg
 * @author    Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides the base exception
 */
require_once 'Hg/Exception.php';

/**
 * Provides access to the Mercurial executable
 */
require_once 'Hg/Executable.php';

/**
 * Provides access to the SCM repository
 */
require_once 'Hg/Container/Repository.php';

/**
 * Interfaces with the classes which implement the commands
 */
require_once 'Hg/CommandProxy.php';

/**
 * Base class to begin the fluent API
 *
 * This package interfaces with the Mercurial command-line binary, which
 * must be installed on the same system as this package. The author of Mercurial
 * is on record preferring that all non-python programs interface with the
 * CLI binary.
 *
 * There are no C-bindings, so a PECL extension is unlikely, as is a pure PHP
 * implementation due to the tremendous workload involved in keeping up with
 * changes Mercurial.
 *
 * PHP version 5
 *
 * @category  VersionControl
 * @package   Hg
 * @author    Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/VersionControl_Hg
 *
 * Usage:
 * <code>
 * require_once 'VersionControl/Hg.php';
 * $hg = new VersionControl_Hg('/path/to/repository');
 * </code>
 * Setting the repository also automatically finds and sets the local
 * installation of the Mercurial binary it will use.
 *
 * Of course, you may explicitly set the executable you wish to use:
 * <code>
 * $hg->setExecutable('/path/to/hg');
 * or
 * $hg->executable = '/path/to/hg';
 * </code>
 * A failed explicit setting will not clear the automatically set executable.
 *
 * You may also provide a location of a repository after instantiation:
 * <code>
 * require_once 'VersionControl/Hg.php';
 * $hg = new VersionControl_Hg();
 * $hg->setRepository('/path/to/repository');
 * or
 * $hg->repository = '/path/to/repository';
 * </code>
 *
 * Calling all commands other than 'version' without having already set a
 * valid repository will raise an exception.
 */
class VersionControl_Hg
{
    /**
     * The repository Hg will act upon
     *
     * @var VersionControl_Hg_Container_Repository
     */
    protected $_repository;

    /**
     * Constructor
     *
     * Assumes to be working on a local filesystem repository
     *
     * @param string $repository is the path to a mercurial repo (optional)
     * @return void
     */
    public function __construct($repository = null)
    {
        $this->setExecutable();

        if ($repository === null) {
            throw new VersionControl_Hg_Exception(
                VersionControl_Hg_Exception::NO_REPOSITORY
            );
        }

        $this->setRepository($repository);
    }

    /**
     * Sets the repository property
     *
     * @param string $repository is the path to a valid Mercurial repository
     *
     * @return void
     * @see $_repository
     */
    public function setRepository($repository)
    {
        $this->_repository = new VersionControl_Hg_Container_Repository($repository);
    }

    /**
     * Returns the repository property
     *
     * @return VersionControl_Hg_Container_Repository
     */
    public function getRepository()
    {
        if ( $this->_repository instanceof VersionControl_Hg_Container_Repository) {
            return $this->_repository;
        } else {
            throw new VersionControl_Hg_Exception(VersionControl_Hg_Exception::INVALID_REPOSITORY);
        }
    }

    /**
     * Proxy down to the command class
     *
     * This also allows programmers to use both
     * <code>
     * $executables_object = $hg->executable;
     * and
     * $executables_object = $hg->getExecutable();
     * </code>
     * to both return an instance of VersionControl_Hg_Executable, for example.
     *
     * @param string $method is the function being called
     * @param array  $arguments are the parameters passed to that function
     *
     * @return VersionControl_Hg_Command_Abstract
     *
     * implemented commands:
     * @method array version()
     * @method array status()
     * @method array archive()
     */
    public function __call($method, $arguments)
    {
        switch (strtolower(substr($method, 0, 3))) {
            //@TODO how do we know what we are setting?
            case 'set':
                $object = strtolower(substr($method, 3));
                //Ex. $hg->getExecutable() => $hg->executable->getPath()
                break;
            //@TODO do we 'get' the object or the property?
            case 'get':
                break;
            default:
                /* proxy to Hg/Command.php */
                $hg_command = new VersionControl_Hg_CommandProxy($this);
                    //must pass an instance of VersionControl_Hg to provide it with
                    //the executable and repository
                return call_user_func_array(array($hg_command, $method), $arguments);
                break;
        }

    }

    /**
     * Returns an object, usually handled by a subcommand
     *
     * A $name is a lowercase, short name of the object:
     * $hg->executable is an instance of VersionControl_Hg_Executable and can
     * be echoed to invoke __toString() to get a pertinent piece of metadata.
     *
     * Instead of calling <code>$hg->getVersion();</code>, we simplify:
     * <code>$version = $hg->version</code>.
     *
     * @param string $name is the object to get
     */
    public function __get($name) {
        /* Instantiate the object corresponding to the short name
         * most are commands, some are top-level objects */
        switch ($name) {
            case 'repository':
                /* Singleton let's us use an instance or create a new one if
                 * not instantiated
                 *
                 * We're ok with a null argument here since to even use
                 * this, $hg would already have to be instantitated
                 * successfully with a repo argument.
                 */
                return VersionControl_Hg_Container_Repository::construct();
                break;
            case 'executable':
                /* Singleton let's us use an instance or create a new one if
                 * not instantiated */
                return VersionControl_Hg_Executable::construct();
                break;
            default:
                // its a command
                $command = new VersionControl_Hg_CommandProxy($this);
                return call_user_func_array(array($command, $method), array());
                break;
        }
    }

    /**
     *
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value) {
        /* Instantiate the object corresponding to the short name
         * most are commands, some are top-level objects */
        switch ($name) {
            case 'repository':
                /* Singleton let's us use an instance or create a new one if
                 * not instantiated */
                return VersionControl_Hg_Container_Repository::construct($value);
                break;
            case 'executable':
                /* Singleton let's us use an instance or create a new one if
                 * not instantiated */
                return VersionControl_Hg_Executable::construct($value);
                break;
            default:
                //its a command
                $command = new VersionControl_Hg_CommandProxy($this);
                return call_user_func_array(array($command, $method), array());
                break;
        }
    }

    /**
     * Print out the class' properties
     *
     * @return string
     */
    public function __toString()
    {
        /* This automagically calls $this::__get() and then automagically
         * invokes VersionControl_Hg_Executable::__toString() */
        echo 'Executable: ' . $this->executable . "\r\n";
        echo 'Repository: ' . $this->repository . "\r\n";
        echo 'Version: ' . $this->version . "\r\n";
    }
}
