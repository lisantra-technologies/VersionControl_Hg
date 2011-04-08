<?php

/**
 * Exception for Hg commands
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Exceptions
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Exception for Hg commands
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Exceptions
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Exception extends Exception
{
    /**
     * Error constant
     */
    const COMMANDLINE_ERROR = 'commandLineError';

    /**
     * Error messages for humans
     *
     * @var array
     */
    protected $_messages = array(
        'commandLineError' => "The command line returned an error status. Please examine the output of \$object->getCommandString() to see the actual shell command issued.",
    );

    /**
     * Override constructor so we can make exception messages more structured
     * like Zend Framework's.
     *
     * @param string $message is equivalent to the error constants
     */
    public function __construct($message) {
        parent::__construct($this->_messages[$message]);
    }
}
