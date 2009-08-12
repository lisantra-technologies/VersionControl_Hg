<?php

/**
 * Contains the class which interacts with the system's `hg` program.
 *
 *
 * PHP version 5
 *
 * @category VersionControl
 * @package Hg
 * @subpackage
 * @author Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Assumes to be working on a local filesystem repository
 *
 * PHP version 5
 *
 * @category VersionControl
 * @package Hg
 * @subpackage
 * @author Michael Gatto <mgatto@lisantra.com>
 * @copyright 2009 Lisantra Technologies, LLC
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version Hg: $Revision$
 * @link http://pear.php.net/package/VersionControl_Hg
 *
 * Usage:
 * <code>
 * $hg = new hg();
 * </code>
 */
class Hg
{
    /**
     * Use the executable found in the default installation location
     */
    const DEFAULT_EXECUTABLE = "deault";

    /**
     * error constant for when the mercurial executable cannot be found
     */
    const ERROR_HG_NOT_FOUND = 'notFound';

    /**
     * The Mercurial binary being used
     *
     * There may well be multiple versions in use; lets track which one
     * I am using so the user knows which one is being used.
     */
    private $_hg = null;

    /**
     * The version of the Mercurial executable.
     *
     * @var float
     */
    private $_version = null;

    /**
     * Path to a local Mercurial repository
     *
     * @var string
     */
    private $_path_to_repository = null;

    /**
     *
     * @var array
     */
    protected $messages = array(
        'notFound' => 'Mercurial could not be found on this system',
    );

    /**
     *
     * @param string $path is the path to a mercurial repo (optional)
     * @return
     */
    public function __construct($path = null)
    {
        $this->setHgExecutable($hg = self::DEFAULT_EXECUTABLE);
        $this->setVersion();
        //we also let users call setRepository($path) if they want
        if ($path !== null) {
            $this->setRepository($path);
        }

    }

    /**
     * Returns the version of the Mercurial executable.
     *
     * Implements the --version switch of the command-line client.
     * Possible values are:
     * (version 1.1), (version 1.1+20081220), (version 1.1+e54ac289bed), (unknown)
     *
     * @return array
     * @see $_version
     */
    public function setVersion($version_data = NULL)
    {
        /*
         * set input source
         */
        if ( $version_data === NULL ) {
            exec($this->getHgExecutable . ' --version', $output);
            $ver_string = $output[0];
        } else {
            $ver_string = $version_data;
        }

        /*
         * handle bad input
         */
        if ( preg_match('/\(.*\)/', $ver_string, $ver_match) == 0 ) {
            throw new VersionControl_Hg_Exception(
                'Unrecognized version data'
            );
        } //@todo replace with a constant and message

        /*
         * this is only processed if no exception
         */
        $version['raw'] = trim( substr( $ver_match[0], 8, strlen( $ver_match[0] ) ) );
        //replace the parenthesis because my regex fu is out to lunch.
        $version['raw'] = str_replace( '(' , '', $version['raw'] );
        $version['raw'] = str_replace( ')' , '', $version['raw'] );
        //break up string into version components
        //does the version have a date after the version number?
        if ( strstr($version['raw'], '+') ) {
            $ver_parts = explode('+', $version['raw']);
            //handle if the text after '+' is a changeset, not a date
            if ( date_parse($ver_parts[1]) ) {
                $version['date'] = $ver_parts[1];
            }
            else{
               $version['changeset'] = $ver_parts[1];
            }
        }
        else {
            $ver_parts[0] = $version['raw'];
        }

        $version['complete'] = $ver_parts[0];

        $version_tmp = explode('.', $ver_parts[0]);

        $version['major'] = $version_tmp[0];
        $version['minor'] = $version_tmp[1];

        $this->_version = $version['raw'];

        return true;
    }

    /**
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
            throw new VersionControl_Hg_Exception(
                'No Hg version has yet been set!'
            );
        }

        return $this->_version;
    }

    /**
     *
     *
     * @return array
     */
    public function setHgExecutable()
    {
        $executables = array();

        $paths = split(PATH_SEPARATOR, $_ENV['PATH']);
        foreach ($paths as $a_path) {
            $executables[$a_path] = is_executable(
                $a_path . DIRECTORY_SEPARATOR . 'hg'
            );
        }

        if ( ! count($executables) > 0) {
            throw new VersionControl_Hg_Exception(
                $messages[self::ERROR_HG_NOT_FOUND]
            );
        }

        //@todo need a better algorithm to decide.
        //list the default installation paths per platform and array_merge them?
        $this->_hg = $executables[0] . 'hg';
            //@todo if win32, append '.exe' ?

        return true;
    }

    public function getHgExecutable()
    {
        /*
         * I don't want programmers to have to test for null,
         * especially when this is auto-set in the constructor
         */
        if ( $this->_hg === null ) {
            throw new VersionControl_Hg_Exception(
                'No Hg executable has yet been set!'
            );
        }

        return $this->_hg;
    }

    /**
     * Proxy down to the repository class
     *
     * @param string $method
     * @param array $args
     * @return
     */
    public function __call($method, array $args)
    {
        //@todo use an autoloader!
        require_once 'Hg/Repository.php';

        $repo = new Hg_Repository();

        call_user_func_array($repo->$method, $args);

        //actually, if it doesn't exist, then we want it to "bubble down"
        //so Hg_Repository will proxy it to the command factory class.
        //this way, users can do this:
        //$repo = new VersionControl_Hg('/path/to/hg');
        //$repo->export(HG_ALL)->to('/home/myself/releases/')->as(HG_ZIP)
        /*if (method_exists($repo, $method)) {

        }*/
    }

}
