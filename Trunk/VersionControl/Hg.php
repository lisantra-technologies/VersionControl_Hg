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

require_once 'Hg/Exception.php';
require_once 'Hg/Container/Executable.php';
require_once 'Hg/Container/Repository.php';
require_once 'Hg/CommandProxy.php';

/**
 * Assumes to be working on a local filesystem repository
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
 * $hg = new VersionControl_Hg();
 * </code>
 *
 * Or, provide a location of a repository:
 * <code>
 * $hg = new VersionControl_Hg('/path/to/repository');
 * </code>
 */
class VersionControl_Hg
{
    /**
     * The executable this package will use
     *
     * @var VersionControl_Hg_Container_Executable
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
     * @param string $repository is the path to a mercurial repo (optional)
     *
     * @return void
     */
    public function __construct($repository = null)
    {
        $this->setExecutable();

        if ($repository !== null) {
            $this->setRepository($repository);
        }

    }

    /**
     * Sets the repository property
     *
     * @param string $repository is the path to a valid Mercurial repository
     *
     * @return void
     * @see this::_repository
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
            throw new VersionControl_Hg_Exception(
                'The repository has not been set'
            );
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
        $this->_executable = new VersionControl_Hg_Container_Executable($path);
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
        if ( $this->_executable instanceof VersionControl_Hg_Container_Executable) {
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
     * Print out the class' properties
     *
     * @return string
     */
    public function __toString()
    {
        echo 'Executable in use: ' . $this->_executable->getPath() . "\r\n";
        echo 'Repository in use: ' . $this->_repository->getPath() . "\r\n";
    }
}
