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
 * @version   Hg: $Revision$
 * @link      http://pear.php.net/package/VersionControl_Hg
 */

require_once 'Hg/Exception.php';
require_once 'Hg/Command.php';

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
 * @version   Hg: $Revision$
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
     * Use the executable found in the default installation location
     */
    const DEFAULT_EXECUTABLE = "default";

    /**
     * Use the executable specified by the user
     */
    const CUSTOM_EXECUTABLE = "custom";

    /**
     * error constant for when the mercurial executable cannot be found
     */
    const ERROR_HG_NOT_FOUND = 'notFound';

    /**
     * The Mercurial binary being used
     *
     * There may well be multiple versions in use; lets track which one
     * I am using so the user knows which one is being used.
     *
     * It is labeled as $hg because this is the symbology adopted by the
     * Mercurial project, since HG is the chemical symbol of the element:
     * Mercury.
     */
    protected $hg = null;

    /**
     * The version of the Mercurial executable.
     *
     * @var float
     */
    private $_version = null;

    /**
     *
     * @var array
     */
    protected $messages = array(
        'notFound' => 'Mercurial could not be found on this system',
    );

    /**
     * Constructor
     *
     * @param string $path is the path to a mercurial repo (optional)
     *
     * @return void
     */
    public function __construct($path = null)
    {
        $this->setHgExecutable();
        $this->setVersion();
        //we also let users call setRepository($path) if they want
        if ($path !== null) {
            include_once 'Hg/Repository.php';
            $repository = new VersionControl_Hg_Repository($path);
        }

    }

    //@todo this *really* should be proxied with __call instead of implementing
    //even a call to the command in this class...
    /**
     * Proxy to setVersion; distinguish between accessor getVersion()
     * and command 'version'
     *
     * @param array $options are the runtime switches for this command
     *
     * @return string
     */
    public function version(array $options)
    {
        $command = new VersionControl_Hg_Command($this);
        return $command->version($options);
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
        $command = new VersionControl_Hg_Command($this);
        $this->_version = $command->version(array('quiet' => null));
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
                'No Hg version has yet been set!'
            );
        }

        return $this->_version;
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
     * $hg = new VersionControl_Hg();
     * $hg->setHgExecutable('/path/to/your/mercurial/binary');
     * </code>
     *
     * @param string $binary is the full path of the mercurial executable
     *
     * @return string
     */
    public function setHgExecutable($hg = self::DEFAULT_EXECUTABLE)
    {
        $executables = array();
        $default_installation = array(

        );

        /*
         * is one of "Windows_NT",
         * $_ENV under the windows xp cmd.com was completely empty
         * using Php 5.2.9-cli
         */
        switch ($_SERVER['OS']) {
            case 'Windows_NT':
                $hg = 'hg.exe';
                break;
            default:
                $hg = 'hg';
                break;
        }

        $paths = split(PATH_SEPARATOR, $_SERVER['Path']);
        foreach ($paths as $a_path) {
            if (is_executable($a_path . DIRECTORY_SEPARATOR . $hg)) {
                $executables[] = $a_path . DIRECTORY_SEPARATOR . $hg;
            }
        }

        if ( count($executables) === 0) {
            throw new VersionControl_Hg_Exception(
                $this->messages[self::ERROR_HG_NOT_FOUND]
            );
        }

        //@todo need a better algorithm to decide.
        //list the default installation paths per platform and array_merge them?
        $this->hg = array_shift($executables);

        return true;
    }

    /**
     * Get the full path of the currently used Mercurial executable
     *
     * @return string
     */
    public function getHgExecutable()
    {
        /*
         * I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor
         */
        if ( $this->hg === null ) {
            //@todo replace with a constant and a $message entry
            throw new VersionControl_Hg_Exception(
                'No Hg executable has yet been set!'
            );
        }

        return $this->hg;
    }

    /**
     * Proxy down to the repository class
     *
     * @param string $method    is the function being called
     * @param array  $arguments are the parameters passed to that function
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        //@todo use an autoloader!
        include_once 'Hg/Repository.php';

        $repo = new VersionControl_Hg_Repository();

        call_user_func_array($repo->$method, $arguments);

        //actually, if it doesn't exist, then we want it to "bubble down"
        //so Hg_Repository will proxy it to the command factory class.
        //this way, users can do this:
        //$repo = new VersionControl_Hg('/path/to/hg');
        //$repo->export(HG_ALL)->to('/home/myself/releases/')->as(HG_ZIP)
        /*if (method_exists($repo, $method)) {

        }*/
    }

}
