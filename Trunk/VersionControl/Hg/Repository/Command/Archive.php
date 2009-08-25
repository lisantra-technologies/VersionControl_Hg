<?php
/**
 * Contains the definition of the VersionControl_Hg_Repository_Command_Archive
 * class
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
 * @see         VersionControl_Hg_Repository_Command_Archive::
 */

require_once 'Interface.php';
require_once 'Exception.php';

/**
 * Exports repository to a (optionally compressed) archive file.
 *
 * Usage:
 * <code>
 * $hg = new VersionControl_Hg('/path/to/hg');
 * $hg->archive('tip', '/home/myself/releases/', 'tgz');
 * </code>
 *
 * Or, a forthcoming fluid api:
 * <code>
 * $hg->archive('tip')->to('/home/myself/releases/')->as('tgz');
 * </code>
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
class VersionControl_Hg_Repository_Command_Archive
    extends VersionControl_Hg_Repository_Command
    implements VersionControl_Hg_Repository_Command_Interface
{
    /**
     * For when the desired archive format is not one of the supported formats
     */
    const ERROR_UNSUPPORTED_ARCHIVE_TYPE = 'unsupportedArchiveType';

    /**
     * The Mercurial command to execute
     *
     * @var string
     */
    protected $command = 'archive';

    /**
     * Which revision to use in this command
     *
     * @var string
     */
    protected $revision;

    /**
     * Where the archive should be saved to
     *
     * @var string
     */
    protected $destination;

    /**
     * Default archive type is 'files'.
     *
     * @var string
     */
    protected $type;

    /**
     * Possible options this command may have
     *
     * @var mixed
     */
    protected $allowed_options = array(
        'type', 'rev', 'prefix', 'excluding', 'including'
    );

    /**
     * Required options this command needs
     *
     * --type defaults to 'files';
     * --rev defaults to tip;
     * --prefix defaults to ;
     *
     * @var mixed
     */
    protected $required_options = array(
        ''
    );

    /**
     * Valid formats for the archive.
     *
     * This is dictated by the formats which Mercurial natively supports.
     * LZMA would be nice.
     *
     * @var mixed
     */
    private $_valid_archive_types = array(
        'nothing' => 'files',
        'tar' => 'tar',
        'bzip2' => 'tbz2',
        'gzip' => 'tgz',
        'zip' => 'zip',
    );

    /**
     * Constructor
     *
     * @param   VersionControl_Hg_Repository $repository
     * @return  void
     */
    public function __construct(VersionControl_Hg_Repository $repository)
    {
        $this->container = $repository;
    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/VersionControl_Hg_Command#to($directory)
     */
    public function to($directory)
    {
        $this->setDestination($directory);

        /* for the fluent api */
        return $this;
    }

    /**
     *
     * @param $type
     * @return unknown_type
     */
    public function with($type)
    {
        $this->setArchiveType($type);

        /* for the fluent api */
        return $this;
    }

    /**
     *
     * @return mixed the results of execute()
     */
    public function run()
    {
        return $this->execute();
    }


    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute($params)
    {
        //$global_options = $this->getGlobalOptions(); //implemented in parent
        //$options = array_merge($options, $global_options);

        $this->options['rev'] = $params[0][0][0];//$this->getRevision();
        $this->options['type'] = $this->_valid_archive_types[$params[0][0][2]];

        $this->options['repository'] = $this->container->getRepository();

        $destination = $params[0][0][1] .
            DIRECTORY_SEPARATOR .
            '%b-r%R.' .
            $this->_valid_archive_types[$params[0][0][2]];

        /* If files: destination must not already exist! Else, Hg will report "Permission denied" */
        if (is_file($destination) || is_dir($destination) ) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                'The destination directory already exists, but it should not'
            );
        }

        /* if other than 'files', then it has to be a filename! */


        $command_string = '"'.$this->container->hg->getHgExecutable().'" ' . $this->command;
//var_dump($command_string);

        $modifiers = null;
        foreach ($this->options as $option => $argument) {
            $modifiers .= ' --' . $option . ' ' . $argument;
        }

        /* rtrim so we don't end up with no space before first switch:
           "hg archive--rev tip" */
        $command_string .= rtrim($modifiers);
        $command_string .= " $destination";
var_dump($params);
echo "\r\n";
var_dump($command_string);

        exec($command_string, $output, $command_status);
        //@todo remove the die()...
        ($command_status == 0) or die("returned an error: $command_string");

    }

    /**
     * Mutator for the archive's format
     *
     * @param  string $format
     * @return void
     * @throws VersionControl_Hg_Repository_Command_Exception
     */
    public function setArchiveType($type = 'files')
    {
        if ( ! in_array($type, $this->_valid_archive_types)) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                self::ERROR_UNSUPPORTED_ARCHIVE_TYPE
            );
        }

        $this->type = $this->_valid_archive_types[$type];
    }

    /**
     * Accessor for the archive's format
     *
     * @return string
     * @see $_archive_format
     * @see $_valid_archive_formats
     */
    public function getArchiveType()
    {
        return $this->type;
    }

    public function setDestination($directory)
    {
        if ( empty($directory) ) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                'I was not told where the archive should go to'
            );
        }

        $this->destination = $directory;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setRevision($revision = 'tip')
    {
        $this->revision = $revision;
    }

    public function getRevision()
    {
        return $this->revision;
    }

}
