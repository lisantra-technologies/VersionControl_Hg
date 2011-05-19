<?php
/**
 * Contains definition of Interface for Hg commands
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2011 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Interface for Hg commands
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2911 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
interface VersionControl_Hg_Command_Interface
{
    public function execute(array $options = null);
}
