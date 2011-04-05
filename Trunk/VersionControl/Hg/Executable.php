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
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
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
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
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
     * Error messages for humans
     *
     * @var array
     */
    protected $_messages = array(
        'notFound' => 'Mercurial could not be found on this system',
        'yetUnset' => 'The Mercurial executable has not yet been set; that is unusual!',
    );

    /**
     * Constructs class with a path to the executable
     *
     * @param string $path
     * @return void
     */
    public function __construct($path) {
        $this->setPath($path);
        $this->setExecutable();
        //$this->setVersion();
    }

    /**
     * Sets the user-defined path on which to search for an Hg executable
     *
     * @param $path string
     * @return void
     */
    public function setPath($path) {
        $this->_path = $path;
    }

    /**
     * Gets the user-defined path
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
     * @return void
     * @throws VersionControl_Hg_Container_Exception
     */
    public function setExecutable() {
        $executables = array();

        /* list the default installation paths per platform */
        $default_installation = array(
            'WINNT' => 'C:\Program Files\Mercurial',
            'WIN32' => 'C:\Program Files\Mercurial',
            'Windows' => 'C:\Program Files\Mercurial',
            'Linux' => '/usr/bin',
            'FreeBSD' => '', // /usr/local/bin ?
            'NetBSD' => '',
            'OpenBSD' => '',
            'SunOS' => '',
            'Darwin' => '',
            'MacOS' => '',
            'HP-UX' => '',
            'IRIX64' => '',
        );
        //use PHP_OS (best), php_uname('s'), $_SERVER['OS']
        /*
         * set the binary name per platform
         */
        switch ($_SERVER['OS']) {
            case 'Windows_NT':
                $binary = 'hg.exe';
                break;
            default:
                $binary = 'hg';
                break;
        }

        $path = $this->getPath();

        if (null !== $path) {
            /* use the user provided path to an executable */
            if (is_executable($path . DIRECTORY_SEPARATOR . $binary)) {
                $executables[] = $path . DIRECTORY_SEPARATOR . $binary;
            }
        } else {
            /* iterate through the system's path to automagically find
             * an executable */
            $paths = split(PATH_SEPARATOR, $_SERVER['Path']);

            foreach ($paths as $a_path) {
                if (is_executable($a_path . DIRECTORY_SEPARATOR . $binary)) {
                    $executables[] = $a_path . DIRECTORY_SEPARATOR . $binary;
                }
            }

            if ( count($executables) === 0) {
                throw new VersionControl_Hg_Container_Exception(
                    $this->_messages[self::ERROR_HG_NOT_FOUND]
                );
            }
        }

        /* use only the first instance found of a mercurial executable */
        $this->_executable = array_shift($executables);
    }

    /**
     * Get the full path of the currently used Mercurial executable
     *
     * @return string
     */
    public function getExecutable()
    {
        /*
         * I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor
         */
        if ( $this->_executable === null ) {
            //@todo replace with a constant and a $message entry
            throw new VersionControl_Hg_Container_Exception(
                $this->_messages[self::ERROR_HG_YET_UNSET]
            );
        }

        return $this->_executable;
    }

    /**
     * Returns the version of the Mercurial executable.
     *
     * Implements the version command of the command-line client.
     * Possible values are:
     * (version 1.1), (version 1.1+20081220), (version 1.1+e54ac289bed), (unknown)
     *
     * @return array
     * @see $_version
     */
    public function setVersion()
    {
        //@todo why am I passing $this to the Command?
        $command = new VersionControl_Hg_CommandProxy();

        $this->_version = $command->version()->run(array('quiet' => true));
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
            throw new VersionControl_Hg_Container_Exception(
                'No Hg version has yet been set!'
            );
        }

        return $this->_version;
    }

    /**
     * Print the full path of the system's command line Mrcurial
     */
    public function __toString() {
        return $this->_executable;
    }

}
