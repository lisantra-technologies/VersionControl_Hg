<?php
/**
 * Contains the class definition for VersionControl_Hg_CommandProxy
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Include the Exception class manually. I use require because we want a fatal
 * error if the file is not found.
 */
require_once 'Exception.php';

/**
 * Instantiates the Mercurial command and passes an instance of
 * VersionControl_Hg into the constructor of each command.
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_CommandProxy
{
    /**
     * Implemented commands
     *
     * @var array
     */
    protected $allowed_commands = array(
        'version', 'archive', 'status',
    );

    /**
     * The command class to be instantiated
     *
     * @var VersionControl_Hg_Command_Abstract
     */
    protected $_command;

    /**
     * The parent, core object
     *
     * @var VersionControl_Hg
     */
    protected $_hg;

    /**
     * Constructor
     *
     * @param   VersionControl_Hg $hg
     */
    public function __construct(VersionControl_Hg $hg) {
        $this->_hg = $hg;
    }

    /**
     * Sets the command property
     *
     * @param $command VersionControl_Hg_Command_Interface
     */
    public function setCommand(VersionControl_Hg_Command_Interface $command) {
    	$this->_command = $command;
    }

    /**
     * Returns the command property
     *
     * @return VersionControl_Hg_Command_Interface
     */
    public function getCommand() {
    	return $this->_command;
    }

    /**
     * Proxies to the actual implementations of the commands
     *
     * @param $method string
     * @param $arguments array
     * @return VersionControl_Hg_Command_Interface
     * @throws VersionControl_Hg_Exception
     */
    public function __call($method, $arguments = null) {
    	if ( ! in_array($method, $this->allowed_commands) ) {
            return new VersionControl_Hg_Exception(
                'The command is unrecognized or unimplemented'
            );
        }
        $class = 'VersionControl_Hg_Command_' . ucfirst($method);
        /* We don't want relative paths because of Php's seemingly odd
         * handling of relative includes within includes */
        require_once dirname(__FILE__) . '/Command/' . ucfirst($method) . ".php";
        /* this tests only if the class exists in the included file */
        if ( ! class_exists($class, false) ) {
            throw new VersionControl_Hg_Exception(
                "Sorry, The command \'{$method}\' is not implemented, or
                 you called `run()` without first issuing a valid command"
            );
        }

        $this->_command = new $class($arguments);
        $this->_command->setContainer($this->_hg);

        return $this->_command; //for fluent API
    }
}
