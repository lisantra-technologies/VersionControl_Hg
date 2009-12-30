<?php
/**
 * Contains the definition of the Version command
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

require_once 'Interface.php';
require_once 'Abstract.php';
require_once 'Exception.php';

/**
 * Implements the version command
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Command
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Version
    extends VersionControl_Hg_Command_Abstract
    implements VersionControl_Hg_Command_Interface
{
	/**
	 * The name of the mercurial command implemented here
	 *
	 * @var string
	 */
    protected $command = 'version';

    /**
     * There are no required options for this command
     *
     * Redefine it to an empty array to prevent parent from passing on any
     * possible unnecessary required commands.
     *
     * @var mixed
     */
    protected $required_options = array();

    /**
     * Constructor
     *
     * @param   VersionControl_Hg $hg
     * @return  void
     */
    public function __construct($param = null) {

    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute(array $options)
    {
        //process options array; everything should be in long format.
        $modifiers = null;
        //this $options[0] thingy is because __call makes the arguments into an array
        if ( is_array($options[0])) {
            foreach ($options[0] as $option => $argument) {
                $modifiers .= ' --' . $option . ' ' . $argument;
            }
        } elseif ( is_string($options[0]) ) {
            //we want only a scalar and not an object nor a null
            $modifiers .= ' --' . $options[0];
        }
        //$command_string = escapeshellcmd();
        $command_string = '"' . $this->container->getExecutable() . '" ' . $this->command;
        $command_string .= rtrim($modifiers);

        exec($command_string, $output, $command_status);

        //@todo remove the die()...
        ($command_status === 0) or die("returned an error: $command_string");

        $ver_string = $output[0];

        /* handle bad input */
        //@todo replace with a constant and message
        if ( preg_match('/\(.*\)/', $ver_string, $ver_match) == 0 ) {
            throw new VersionControl_Hg_Command_Exception(
                'Unrecognized version data'
            );
        }

        $version['raw'] = trim(substr($ver_match[0], 8, strlen($ver_match[0])));
        //replace the parenthesis because my regex fu is out to lunch.
        $version['raw'] = str_replace('(' , '', $version['raw']);
        $version['raw'] = str_replace(')' , '', $version['raw']);
        //break up string into version components
        //does the version have a date after the version number?
        if ( strstr($version['raw'], '+') ) {
            $ver_parts = explode('+', $version['raw']);
            //handle if the text after '+' is a changeset, not a date
            //@todo replace date_parse() this to remove dependency on Php 5.2.x
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

        return $version['raw'];
    }
}
