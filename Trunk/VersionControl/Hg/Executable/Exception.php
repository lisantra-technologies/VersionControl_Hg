<?php

/**
 * Exception for Hg executables
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
 * Exception for Hg executables
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
class VersionControl_Hg_Executable_Exception extends VersionControl_Hg_Exception
{
    /**
     * error constant for when the mercurial executable cannot be found
     */
    const ERROR_HG_NOT_FOUND = 'notFound';

    /**
     * error constant for when operations are called before setting the Hg
     * executable. Should not normally happen since its set in the constructor
     * and throws an exception when an executable cannot be found.
     */
    const ERROR_HG_YET_UNSET = 'yetUnset';

    protected $_messages = array();

}
