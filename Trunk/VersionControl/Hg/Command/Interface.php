<?php

/**
 * Interface for Hg commands
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Interfaces
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
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
 * @subpackage  Interfaces
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
interface VersionControl_Hg_Command_Interface
{
    public function execute(array $options = null);
}
