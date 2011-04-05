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
 * @version   SVN: 0.3
 * @link      http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides the base exception
 */
require_once 'Hg/Exception.php';

/**
 * Provides access to the Mercurial executable
 */
require_once 'Hg/Container/Executable.php';

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
 * @version   Release:
 * @link      http://pear.php.net/package/VersionControl_Hg
 *
 * Usage:
 * <code>
 * require_once 'VersionControl/Hg.php';
 * $hg = new VersionControl_Hg('/path/to/repository');
 * </code>
 *
 * Or, provide a location of a repository after instantiation:
 *
 * <code>
 * require_once 'VersionControl/Hg.php';
 * $hg = new VersionControl_Hg();
 * $hg->setRepository('/path/to/repository');
 * </code>
 *
 * Calling all commands other than 'version' without having already set a
 * valid repository will raise an exception.
 */
class VersionControl_Hg
{
    /**
     * The executable this package will use
     *
     * @var VersionControl_Hg_Executable
     */
    protected $_executable;

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
     *
     * @return void
     */
    public function __construct($repository = null)
    {
        $this->setExecutable();

        if ($repository === null) {
            throw new VersionControl_Hg_Exception(VersionControl_Hg_Exception::NO_REPOSITORY);
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
     * Set the Hg executable's path manually
     *
     * If you need to specifiy a particular Hg executable to use, then pass in
     * the full path to Mercurial as a paramter of this function.
     *
     * It could have been passed into VersionControl_Hg's constructor, but I
     * feel it is uncommon enough to pass in a custom Hg path that it could
     * be relegated to a separate function call.
     *
     * Usage:
     * <code>
     * $hg = new VersionControl_Hg('/path/to/local/repository');
     * //The executable was already automatically found, let's manually reset
     * $hg->setExecutable('/path/to/your/mercurial/binary/hg.exe');
     * </code>
     *
     * @param string $path is the full path of the mercurial executable
     *
     * @return string
     */
    public function setExecutable($path = null)
    {
        $this->_executable = new VersionControl_Hg_Executable($path);
    }

    /**
     * Gets the full path and name of the Mercurial executable in use
     *
     * Usage:
     * <code>
     * $hg = new VersionControl_Hg('/path/to/local/repository');
     * echo $hg->getHgExecutable();
     * </code>
     *
     * @return VersionControl_Hg_Container_Executable
     * @throws VersionControl_Hg_Exception
     */
    public function getExecutable()
    {
        if ( $this->_executable instanceof VersionControl_Hg_Executable) {
            return $this->_executable;
        } else {
            throw new VersionControl_Hg_Exception(
                'The executable has not been set'
            );
        }
    }

    /**
     * Proxy down to the command class
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
        //proxy to Hg/Command.php
        $hg_command = new VersionControl_Hg_CommandProxy($this);
            //must pass an instance of VersionControl_Hg to provide it with
            //the executable and repository

        return call_user_func_array(array($hg_command, $method), $arguments);
    }

    /**
     * Returns a property, usually handled by a subcommand
     *
     * Instead of calling <code>$hg->getVersion();</code>, we simplify:
     * <code>$version = $hg->version</code>.
     *
     * @param string $param is the property to get
     */
    public function __get($param) {
        $method = 'get' . ucfirst($param);
        //proxy to Hg/Command.php
        $hg_command = new VersionControl_Hg_CommandProxy($this);

        return call_user_func_array(array($hg_command, $method), array());
    }

    /**
     * Print out the class' properties
     *
     * @return string
     */
    public function __toString()
    {
        echo 'Executable: ' . $this->_executable->getPath() . "\r\n";
        echo 'Repository: ' . $this->_repository->getPath() . "\r\n";
    }
}
