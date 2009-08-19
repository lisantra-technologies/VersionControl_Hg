<?php

/**
 *
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Commands
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */

require_once 'Interface.php';
require_once 'Exception.php';

/**
 *
 *
 * PHP version 5
 *
 * @category    VersionControl
 * @package     Hg
 * @subpackage  Commands
 * @author      Michael Gatto <mgatto@lisantra.com>
 * @copyright   2009 Lisantra Technologies, LLC
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version     Hg: $Revision$
 * @link        http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Version
    extends VersionControl_Hg_Command
    implements VersionControl_Hg_Command_Interface
{
    /**
     *
     * @var string
     */
    protected $command = 'version';

    /**
     *
     * @var mixed
     */
    protected $required_options = array();

    /**
     *
     * @param   VersionControl_Hg $hg
     *
     * @return  void
     */
    public function __construct(VersionControl_Hg $hg)
    {
        $this->container = $hg;
    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute($params)
    {
        //$this->addOptions($params);
        //$params = $this->getOptions();

        //process options array; everything should be in long format.
        // the leading space in the join param: ' --' is essential.
        //as is the blank space between hg and $command
        //$this->_result = exec( 'hg' . ' ' . $command . join( ' --', $this->getOptions() ), $this->_output );


        //@todo but, will this syntax work on Unix?
        //$command_string = escapeshellcmd($this->hg->getHgExecutable()) . ' ' . $this->_command;
        $command_string = '"'.$this->container->getHgExecutable().'" ' . $this->command;

//var_dump($command_string);

        $modifiers = null;
        foreach ($params as $option => $argument) {
            $modifiers .= ' --' . $option . ' ' . $argument;
        }

        $command_string .= rtrim($modifiers);

//var_dump($command_string);// die;

        exec($command_string, $output, $command_status);
        //@todo remove the die()...
        ($command_status == 0) or die("returned an error: $command_string");

//var_dump($output);

        $ver_string = $output[0];

        /*
         * handle bad input
         */
        if ( preg_match('/\(.*\)/', $ver_string, $ver_match) == 0 ) {
            throw new VersionControl_Hg_Command_Exception(
                'Unrecognized version data'
            );
        } //@todo replace with a constant and message

        $version['raw'] = trim(substr($ver_match[0], 8, strlen($ver_match[0])));
        //replace the parenthesis because my regex fu is out to lunch.
        $version['raw'] = str_replace('(' , '', $version['raw']);
        $version['raw'] = str_replace(')' , '', $version['raw']);
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

        return $version['raw'];
    }
}
