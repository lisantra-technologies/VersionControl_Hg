<?php
/**
 * Contains the definition of the Executable class
 *
 * PHP version 5
 *
 * @category   VersionControl
 * @package    Hg
 * @subpackage Executable
 * @author     Michael Gatto <mgatto@lisantra.com>
 * @copyright  2011 Lisantra Technologies, LLC
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Provides Exceptions for the Executable
 */
require_once 'Executable/Exception.php';

/**
 * Provides access to the Hg executable
 *
 * This is the de-facto parent container of all operations
 *
 * PHP version 5
 *
 * @category   VersionControl
 * @package    Hg
 * @subpackage Executable
 * @author     Michael Gatto <mgatto@lisantra.com>
 * @copyright  2011 Lisantra Technologies, LLC
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @link       http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Executable
{
    /**
     * Use the executable found in the default installation location
     */
    const DEFAULTEXECUTABLE = "default";

    /**
     * Use the executable specified by the user
     */
    const CUSTOMEXECUTABLE = "custom";

    /**
     * Base class in this package which provides access to the repository and
     * command objects
     *
     * @var VersionControl_Hg
     */
    protected $hg;

    /**
     * Storage var for the singleton
     *
     * @var VersionControl_Hg_Executable
     */
    private static $_instance;

    /**
     * Path to the excutable binary
     *
     * @var string
     */
    protected $path;

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
    protected $executable;

    /**
     * The version of the Mercurial executable.
     *
     * @var float
     */
    protected $version;

    /**
     * Capabilities per Mercurial version
     *
     * Format is 'command:option' or 'feature:subfeature' to show when it was
     * added to Mercurial
     *
     * @var mixed
     */
    protected $capabilities = array(
        //1.2 released 2009-03-04
        /* '...:files' is an internal VersionControl_Hg option name used for
         *  specifying multiple files */
        '1.2' => array('commit:close-branch','convert:perforce', 'diff:change'),
        '1.3' => array(
            'subrepos','merge:preview','update:check', 'branches:closed',
            'heads:closed'
        ),
        '1.4' => array('summary','diff:stat', 'diff:reverse', 'clone:updaterev'),
        '1.5' => array(
            'import:files','log:xml','rebase:detach','subrepos:svn',
            'clone:branch', 'bundle:branch', 'incoming:branch',
            'outgoing:branch', 'pull:branch', 'push:branch'
        ),
        '1.6' => array('log:branch'),
        '1.7' => array(
            'strip:keep','strip:files','strip:revsets','add:subrepos',
            'diff:subrepos', 'incoming:subrepos', 'outgoing:subrepos',
            'status:subrepos','revsets'
        ),
        '1.8' => array('listfile','subrepos:git', 'bookmark'),
            //bookmark was an 3rd-party extension prior to 1.8
        '1.9' => array(
            'filesets', 'manifest:all', 'diff:change:revsets',
            'import:bypass', 'paths:quiet'
        ),
    );

    /**
     * Constructor
     *
     * Finds and sets the system's existing Mercurial executable binary which
     * with all future operations will use.
     *
     * @param VersionControl_Hg $hg   The root object
     * @param string            $path The full path to the user defined
     *                                executable
     *
     * @return void
     */
    private function __construct($hg, $path)
    {
        $this->hg = $hg;

        /* Attempt to set the executable */
        $this->setExecutable($path);

        /* We only set version and path if we found a valid executable */
        $this->setPath();

        /* disabled, since calling a command before setting the executable has
         * finished seems to tear a hole in the unniverse... */
        //$version = $this->getVersion();
        //$this->setVersion($version);
    }

    /**
     * The singleton method
     *
     * @param object $hg   is an instance of VersionControl_Hg
     * @param string $path is the path to the executable to use
     *
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
     * @param string $path is the path to the hg executable
     *
     * @return void
     * @see self::$path
     */
    protected function setPath($path = null)
    {
        if ( empty($path) ) {
            $path = dirname($this->executable);
        }

        $this->path = $path;

        //Fluid API
        return $this;
    }

    /**
     * Gets the path of a valid Mercurial executable already set
     *
     * @return string
     * @see self::$path
     */
    public function getPath()
    {
        return $this->path;
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
     * @param string $path The path to the hg executable
     *
     * @return void
     * @throws VersionControl_Hg_Executable_Exception
     */
    public function setExecutable($path = null)
    {
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
            //@TODO Do we care to use the CUSTOMEXECUTABLE constant in this case??
            /* use the user provided path to an executable */
            if ( isexecutable($path . DIRECTORY_SEPARATOR . $binary) ) {
                $executables[] = $path . DIRECTORY_SEPARATOR . $binary;

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
        $this->executable = array_shift($executables);

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
        if ( empty($this->executable) ) {
            throw new VersionControl_Hg_Executable_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_HG_YET_UNSET
            );
        }

        return $this->executable;
    }

    /**
     * Sets the version of the Mercurial executable
     *
     * Implements the version command of the command-line client.
     * Possible values are:
     * (version 1.1), (version 1.1+20081220), (version 1.1+e54ac289bed), (unknown)
     *
     * @return array
     * @see $version
     */
    protected function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the version of Mercurial we are currently using
     *
     * Because of circular dependcies, we have to call the command here
     * instead of in the constructor, since $this->hg is not yet populated.
     *
     * @return string
     */
    public function getVersion()
    {
        $version = $this->hg->version()->run();

        return $version['raw'];

        /*
         * I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor
         */
        if ( $this->version === null ) {
            //@todo replace with a constant and a $message entry
            throw new VersionControl_Hg_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_NO_VERSION
            );
        }
    }

    /**
     * Return Mercurial's capabilities and supported commands and options
     * for this version.
     *
     * The version may be passed in, but the default is to use the current
     * executable's version.
     *
     * @param string|float $version
     *
     * @return mixed
     */
    public function getCapabilities($version = null) {
        /* Use the executable's version property by default */
        if ( null === $version) {
            $version = (string) $this->getVersion();
        }

        /* Version string must be only major and minor. This stript
         * anything after the last period. We're assuming that
         * the only string passed is a 3-part version string: 1.8.4;
         * What if its a wrong assumption?? */
        $version = substr($version, 0, strrpos($version, '.'));

        /* Ensure the passed-in version string is supported */
        if (! array_key_exists($version, $this->capabilities) ) {
            throw new VersionControl_Hg_Executable_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_VERSION_NOT_SUPPORTED
            );
        }

        /** Break up options from features and subfeatures from features */
        /* initialize an empty array to avoid PHP Notices */
        $capabilities = array();

        $raw_capabilities = $this->capabilities[$version];

        //@TODO handle triples, too: ex. subrepo:git:remote

        foreach ( $raw_capabilities as $capability ) {
            $capability_group = explode(':', $capability);

            /* handle single capabilities without options or sub-features */
            if ( empty($capability_group[1])) {
                array_push($capabilities, $capability_group[0]);
            } else {
                $capabilities[$capability_group[0]] = array($capability_group[1]);
            }
        }

        return $capabilities;
    }

    /**
     * Determines if the required capability is present in the user's
     * Mercurial executable version
     *
     * @param string $capability
     *
     * @return boolean
     */
    public function hasCapability($capability) {
        /* default $has_capability to true so as not to give false positives;
         * the worst that can happen now is that HG cli issues an error */
        $has_capability = true;

        /* Version string must be only major and minor. This stript
         * anything after the last period. We're assuming that
         * the only string passed is a 3-part version string: 1.8.4;
         * What if its a wrong assumption?? */
        $raw_version = $this->getVersion();
        $our_version = substr($raw_version, 0, strrpos($raw_version, '.'));

        /* find the first version to support the $capability; if not found,
         * then assume its a capability. Adapted from code in the php.net
         * manual by giulio.provasi@gmail.com on 30-Apr-2010 09:06,
         * http://php.net/manual/en/function.array-search.php */
        $array_iterator = new RecursiveArrayIterator($this->capabilities);
        $iterator = new RecursiveIteratorIterator($array_iterator);

        while($iterator->valid()) {
            if ( $iterator->current() == $capability ) {
                $capability_version = $array_iterator->key();
            }

            $iterator->next();
        }

        /* This occurs when the capability is not found. We will issue an
         * exception, because capability checking should be explicit per command
         * or option rather than automatic. If it were automatic, we would need
         * to make it return true by default, since not exhaustively cataloging
         * options and features would result in numerous false negatives! */
        if ( ! isset($capability_version) ) {
            throw new VersionControl_Hg_Executable_Exception(
                VersionControl_Hg_Executable_Exception::ERROR_CAPABILITY_UNKNOWN
            );
        }

        if ( (float) $capability_version > (float) $our_version ) {
            $has_capability = false;
        }

        return $has_capability;
    }

    /**
     * Print the full path of the system's command line Mercurial
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getExecutable();
    }

    /**
     * Prevent users to clone the instance
     *
     * @return null
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    /**
     * Get an unaccessible class property
     *
     * @param string $value The class property trying to be gotten
     *
     * @return string
     */
    public function __get($value)
    {
        $method = 'get' . ucfirst($value);

        return $this->$method();
    }

}
