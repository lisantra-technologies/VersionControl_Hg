<?php
/**
 * Contains definition of the Repository class
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Container
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2011 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link 		http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides the abstraction for containers
 */
require_once 'Abstract.php';

/**
 * Provides the container interface
 */
require_once 'Interface.php';

/**
 * Provides the container exception
 */
require_once 'Repository/Exception.php';

/**
 * The Mercurial repository
 *
 * Usage:
 * All calls are proxied from Hg
 * <code>
 * $hg = new VersionControl_Hg('/path/to/repo');
 * $repository = $hg->getRepository()->getPath();
 * or
 * $repository = $hg->repository->delete();
 * </code>
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Container
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2011 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Container_Repository
    extends VersionControl_Hg_Container_Abstract
        implements VersionControl_Hg_Container_Interface
{
    /**
     * The name of all Mercurial repository roots.
     *
     * @FIXME: is this still needed to say?
     * Leading backslash is needed since path may not have a trailing slash.
     *
     * @const string
     */
    const ROOT_NAME = '.hg';

    /**
     * Base class in this package
     *
     * Provides ability to call commands
     *
     * @var VersionControl_Hg
     */
    protected $hg;

    /*
     * Hold an instance of the class
     */
    private static $instance;

    /**
     * Path to a local Mercurial repository
     *
     * @var string
     */
    protected $path;

    /**
     * Repository constructor which currently does nothing.
     *    *
     * @param object $hg   is the root object and as a singleton
     *                     will always be an instance of VersionControl_Hg
     * @param string $path is the full path to the user defined executable
     *
     * @return void
     */
    private function __construct(VersionControl_Hg $hg, $path) {
        $this->setPath($path);
        $this->hg = $hg;
    }

    /**
     * The singleton method
     *
     * @param object $hg   Instance of VersionControl_Hg
     * @param string $path The path to the executable to use
     *
     * @return VersionControl_Hg_Repository
     */
    public static function getInstance($hg = null, $path = null)
    {
        if ( ! isset(self::$instance) ) {
            $singleton_class = __CLASS__;
            self::$instance = new $singleton_class($hg, $path);
        }

        return self::$instance;
    }

    /**
     * FOR UNIT TESTING OF THIS SINGLETON, ONLY!
     *
     * @return null
     */
    public static function reset() {
        self::$instance = NULL;
    }

    /**
     * Sets the path of a Mercurial repository after validating it as a Hg
     * repository
     *
     * @param string $path The path to the hg executable
     * @see self::$path
     *
     * @return VersionControl_Hg
     */
    public function setPath($path = null)
    {
        /* not passing in a path is OK, especially since the programmer may
         * want to call create() */
        if ( is_null($path) ) {
            return;
        }

        if (is_array($path)) {
            $path = $path[0];
        }

        //is it even a real path?
        if ( ! realpath($path)) {
            throw new VersionControl_Hg_Container_Repository_Exception(
                VersionControl_Hg_Container_Repository_Exception::DOES_NOT_EXIST
            );
        }

        /*
         * Let's not guess that the user wants to create a repo if none exists;
         * Throw and exception and let them decide what to do next.
         * Maybe they just gave the wrong path.
         *
         * Line breaks are transmitted to CLI apps; concat the strings to
         * ignore them in output.
         */
        if ( ! $this->isRepository($path)) {
            throw new VersionControl_Hg_Container_Repository_Exception(
                VersionControl_Hg_Container_Repository_Exception::NO_REPOSITORY
            );
        }

        $this->path = $path;

        /* For fluid API */
        return $this;
    }

    /**
     * Checks if $this is in fact a valid
     *
     * @param  string $repo The full repository path.
     *
     * @return boolean
     */
    protected function isRepository($path)
    {
        /*
         * @todo a valid repo has this structure, so test for this:
         * .hg
         *  |---store/
         *        |---data/ (directory)
         *  |---dirstate (file)
         */

        $is_repository = false;

        $repository = $path . DIRECTORY_SEPARATOR . self::ROOT_NAME;

        /* both conditions must be satisfied. */
        if (is_dir($repository) && (! empty($repository))) {
            $is_repository = true;
        }

        return $is_repository;
    }

    /**
     *
     *
     * @return
     */
    public function create($path)
    {
        //@TODO FINISH THIS!
        //must call is_repository on the path first, since setPath was not used in instantiation...?


        $this->_command = new VersionControl_Hg_Command_Init();
        //$this->_command = new Hg_Repository_Command_Init($this);
            //pass $this as dependency injection instead of having
            //Hg_Repository_Command inherit from Hg_Repository?


        //return it so we can chain it
        return $this->_command;
    }

    /**
     * Deletes the repository, effectively removing the working copy from SCM.
     *
     * @return boolean
     */
    public function delete()
    {
        if ( unlink($this->path) ) {
            return true;
        } else {
            //return false;
            throw new VersionControl_Hg_Container_ExceptionException('The repository could not be deleted.');
        }
    }

    /**
     * Prevent users to clone the instance
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
}
