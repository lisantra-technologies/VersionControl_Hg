<?php
/**
 * Contains the definition of the VersionControl_Hg_Repository_Command_Import
 * class
 *
 * PHP version 5
 *
 * @category   VersionControl
 * @package    Hg
 * @subpackage Command
 * @author     Michael Gatto <mgatto@lisantra.com>
 * @copyright  2011 Lisantra Technologies, LLC
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link       http://pear.php.net/package/VersionControl_Hg
 */

/**
 * Import a set of patches into a repository
 *
 *
 * message() --message
 * comitter() --user
 * dated() --date
 * only('working_copy')  --no-commit
 *    --base
 * strip()   --strip
 *           --exact
 *           --similarity
 * show('rejects') or have the command return an array of rejects if any
 *
 * Usage:
 * <code>
 * $hg = new VersionControl_Hg('/path/to/repo');
 * $hg->import('/path/to/patch')->run();
 *
 * $hg->import(array('path' => '/path/to/patch'))->run();
 * </code>
 *
 * or, form a url:
 * <code>
 * $hg->import('http://example.edu/path/to/patch')->run();
 * </code>
 *
 * or, a string:
 * <code>
 * $patch_content = fopen();
 * $hg->import(array('content' => $patch_content))->run();
 * </code>
 * However, Mercurial will do this for you when you pass the path to a
 * patch.
 *
 * or even heredoc and nowdoc:
 *
 *
 * PHP version 5
 *
 * @category   VersionControl
 * @package    Hg
 * @subpackage Command
 * @author     Michael Gatto <mgatto@lisantra.com>
 * @copyright  2011 Lisantra Technologies, LLC
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link       http://pear.php.net/package/VersionControl_Hg
 */
class VersionControl_Hg_Command_Import
    extends VersionControl_Hg_Command_Abstract
        implements VersionControl_Hg_Command_Interface
{
    /**
     * The name of the mercurial command implemented here
     *
     * @var string
     */
    protected $command = 'import';

    /**
     * Required options for this specific command. These may not be required
     * by Mercurial itself, but are required for the proper functioning of
     * this package.
     *
     * @var mixed
     */
    protected $required_options = array(
        'noninteractive' => null,
        'repository' => null,
    );

    /**
     * Permissable options.
     *
     * The actual option must be the key, while 'null' is a value here to
     * accommodate the current implementation of setting options.
     *
     * @var mixed
     */
    protected $allowed_options = array(

    );

    /**
     * Constructor
     *
     * @param mixed             $params Options passed to the Log command
     * @param VersionControl_Hg $hg     Instance of the base object
     *
     * @return void
     */
    public function __construct($params = null, VersionControl_Hg $hg)
    {
        /* Make $hg available to option methods */
        $this->hg = $hg;

        /* should always be called so we have a full array of valid options */
        $this->setOptions($params);
    }

    /**
     * Execute the command and return the results.
     *
     * @param mixed $params The options passed to the Log command
     *
     * @return string
     */
    public function execute(array $params = null, VersionControl_Hg $hg = null)
    {

    }

}
