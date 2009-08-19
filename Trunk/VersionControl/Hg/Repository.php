<?php

/**
 * Contains definition of the Repository class
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Repository
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link 		http://pear.php.net/package/VersionControl_Hg
 */

/**
 * The Mercurial repository
 *
 * Usage:
 * All calls are proxied from Hg
 * <code>
 * $hg = new VersionControl_Hg('/path/to/repo');
 * $repository = $hg->getRepository();
 * </code>
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Repository
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Repository
{
    /**
     * The name of all Mercurial repository roots.
     *
     * Leading backslash is needed since _path may not have a trailing slash.
     *
     * @const string
     */
    const ROOT_NAME = '.hg';

    /**
     * Holds the filesystem path of a Mercurial repository
     *
     * @var string
     * @deprecated replaced by $_path_to_repository
     */
    protected $_repository;

    /**
     * Path to a local Mercurial repository
     *
     * @var string
     */
    protected $path_to_repository = null;

    /**
     * Holds the current state of the Hg object
     *
     * @var string
     */
    public $hg;

    /**
     * Repository constructor which currently does nothing.
     *
     * @todo might be a good place to set the transport method?
     */
    public function __construct(VersionControl_Hg $hg)
    {
        $this->hg = $hg;
    }

    /**
     * Sets the path of a Mercurial repository after validating it as a Hg repo.
     *
     * @param   string $path as a local filesystem path.
     * @return  mixed Repository to enable method chaining
     * @see     $path_to_repository
     */
    public function setRepository($path)
    {
        if (is_array($path)) {
            $path = $path[0];
        }

        //is it even a real path?
        if ( ! realpath($path)) {
            throw new Exception(
                'The path: ' . $path . ' does not exist on this system'
            );
        }

        /*
         * Let's not guess that the user wants to create a repo if none exists;
         * Throw and exception and let them decide what to do next.
         * Maybe they just gave the wrong path.
         */
        if ( ! $this->isRepository($path)) {
            throw new Exception(
                'there is no Mercurial repository at: '
                . $path
                . '. Use $hg->create( \'/path/\' ) to create one and then use getRepository() to act upon it.' );
        }

        $this->path_to_repository = $path;
       // return $this; //for chainable methods.
    }

    /**
     * Returns the path of a Mercurial repository as set by the user.
     * It is validated before being set as a class member.
     *
     * @return  string
     * @see     $path_to_repository
     */
    public function getRepository()
    {
        /*if ( null === $this->_repository ) {
            throw new Exception(
                'There is no repository to return'
            );
        }*/

        return $this->path_to_repository;
    }

    /**
     * Checks if $this is in fact a valid
     *
     * @param   string $repo is the full repository path.
     * @return  boolean
     */
    public function isRepository($path)
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

        /*
         * both conditions must be satisfied.
         */
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
    public function create()
    {
        $this->_command = new VersionControl_Hg_Repository_Command_Init();
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
        if ( unlink(  ) ) {
            return true;
        } else {
            return false;
            //throw new Exception( 'The repository could not be deleted.' );
        }

    }

    /**
     * Proxy calls to the Command class
     *
     * @param   string $method
     * @param   array $arguments
     *
     * @return  mixed
     */
    public function __call($method, $arguments)
    {
        //@todo ensure the names of the arguments and method are the same across
        //all __call()'s in the chain!


        include_once 'Repository/Command.php';
        $command = new VersionControl_Hg_Repository_Command($this);
        $params = $arguments[0]; //prevent too many nested arrays of args.
        $command->$method($arguments);
        //@todo if I return the above statement, maybe I can do the fluent API!
    }

}
