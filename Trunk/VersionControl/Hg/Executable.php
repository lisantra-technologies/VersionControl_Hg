<?php
/**
 * Contains the definition of the Executable class
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides access to the Hg executable
 *
 * This is the de-facto parent container of all operations
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Executable
{
    /**
     * Use the executable found in the default installation location
     */
    const DEFAULT_EXECUTABLE = "default";

    /**
     * Use the executable specified by the user
     */
    const CUSTOM_EXECUTABLE = "custom";

    /**
     * Base class in this package
     *
     * Provides ability to call commands
     *
     * @var VersionControl_Hg
     */
    protected $hg;

    // Hold an instance of the class
    private static $_instance;

    /**
     * Path to the excutable binary
     *
     * @var string
     */
    protected $_path;

    /**
     * The Mercurial binary being used
     *
     * There may well be multiple versions in use; lets track which one
     * I am using so the user knows which one is being used.
     *
     * It is labeled as $hg because this is the symbology adopted by the
     * Mercurial project, since HG is the chemical symbol of the element:
     * Mercury.
     *
     * @var string
     */
    protected $_executable;

    /**
     * The version of the Mercurial executable.
     *
     * @var float
     */
    protected $_version;

    /**
     * Constructor
     *
     * Finds and sets the system's existing Mercurial executable binary which
     * with all future operations will use.
     *
     * @param string $path is the full path to the user defined executable
     * @return void
     */
    private function __construct($hg, $path) {
        /* Attempt to set the executable */
        $this->setExecutable($path);

        /* We only set version and path if we found a valid executable */
        $this->setPath();

        /* disabled, since calling a command before setting the executable has
         * finished seems to tear a hole in the unniverse... */
        //$this->setVersion();
    }

    /**
     * The singleton method
     *
     * @param string $path
     * @return VersionControl_Hg_Executable
     */
    public static function getInstance($hg = null, $path = null)
    {
        if (self::$_instance === null) {
            self::$_instance = new VersionControl_Hg_Executable($hg, $path);
        }

        return self::$_instance;
    }

    /**
     * Sets the path on which the Hg executable exists
     *
     * For informational purposes only; not used to search a path.
     *
     * @param $path string
     * @return void
     * @see self::$_path
     */
    protected function setPath($path) {
        if ( empty($path) ) {
            $path = dirname($this->_executable);
        }

        $this->_path = $path;

        //Fluid API
        return $this;
    }

    /**
     * Gets the path of a valid Mercurial executable already set
     *
     * @return string
     * @see self::$_path
     */
    public function getPath() {
        return $this->_path;
    }

    /**
     * Validates the existance and viability of the Mercurial executable on
     * the system
     *
     * If you need to specifiy a particular Hg executable to use, then pass
     * the full path to Mercurial as a paramter of this function.
     *
     * Usage:
     * <code>$hg->setExecutable('/path/to/hg');</code>
     * A failed reset/change will not clear the previously set executable.
     *
     * @return void
     * @throws VersionControl_Hg_Executable_Exception
     */
    public function setExecutable($path = null) {
        $executables = array();

        /* Set the binary name per platform */
        //@todo use PHP_OS (best), php_uname('s'), $_SERVER['OS']
        switch ($_SERVER['OS']) {
            case 'Windows_NT':
                $binary = 'hg.exe';
                break;
            default:
                $binary = 'hg';
                break;
        }

        if (null !== $path) {
            /* use the user provided path to an executable */
            if ( is_executable($path . DIRECTORY_SEPARATOR . $binary) ) {
                $executables[] = $path . DIRECTORY_SEPARATOR . $binary;

                //@TODO Do we care to use the CUSTOM_EXECUTABLE constant in this case??
            }
        }

        /* If the user supplied path was bad or not supplied, autosearch */
        if ( ( empty($executables) ) || ( null === $path ) ) {
            /* iterate through the system's path to automagically find an
             * executable */
            $paths = explode(PATH_SEPARATOR, $_SERVER['Path']);

            foreach ( $paths as $path ) {
                if (is_executable($path . $binary)) { //DIRECTORY_SEPARATOR .
                    $executables[] = $path . $binary;
                }
            }

            if ( count($executables) === 0 ) {
                throw new VersionControl_Hg_Executable_Exception(
                    VersionControl_Hg_Executable_Exception::ERROR_HG_NOT_FOUND
                );
            }
        }

        /* use only the first instance found of a mercurial executable */
        $this->_executable = array_shift($executables);

        /* For fluid API */
        return $this;
    }

    /**
     * Get the full path of the currently used Mercurial executable
     *
     * @return string
     * @throws VersionControl_Hg_Executable_Exception
     */
    public function getExecutable()
    {
        /* I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor */
        if ( empty($this->_executable) ) {
            throw new VersionControl_Hg_Executable_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_HG_YET_UNSET
            );
        }

        return $this->_executable;
    }

    /**
     * Sets the version of the Mercurial executable
     *
     * Implements the version command of the command-line client.
     * Possible values are:
     * (version 1.1), (version 1.1+20081220), (version 1.1+e54ac289bed), (unknown)
     *
     * @return array
     * @see $_version
     */
    protected function setVersion()
    {
        $this->_version = $this->_base->version()->run('quiet');

        //$command = new VersionControl_Hg_CommandProxy($this->_base);
        //return
        //$version = call_user_func_array(array($command, 'version'), array('quiet'));

        //$version2 = $command->version()->run('quiet');
//var_dump($command, $version, $version2);die;
        // = $command->version()->run('quiet');
    }

    /**
     * Get the version of Mercurial we are currently using
     *
     * @return string
     */
    public function getVersion()
    {
        /*
         * I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor
         */
        if ( $this->_version === null ) {
            //@todo replace with a constant and a $message entry
            throw new VersionControl_Hg_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_NO_VERSION
            );
        }

        return $this->_version;
    }

    /**
     * Print the full path of the system's command line Mercurial
     */
    public function __toString() {
        return $this->getExecutable();
    }

    /**
     * Prevent users to clone the instance
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

}
