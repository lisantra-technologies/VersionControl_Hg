<?php
/**
 * Contains the definition of the VersionControl_Hg_Repository_Command_Archive
 * class
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
 * @see         VersionControl_Hg_Repository_Command_Archive::
 */

/**
 *
 */
require_once 'Interface.php';
require_once 'Exception.php';

/**
 * Exports repository to a (optionally compressed) archive file.
 *
 * Usage:
 * <code>
 * $hg->archive('tip')->to('/home/myself/releases/')->with('tgz')->run();
 * </code>
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
class VersionControl_Hg_Command_Archive
    extends VersionControl_Hg_Command_Abstract
    implements VersionControl_Hg_Command_Interface
{
    /**
     * For when the desired archive format is not one of the supported formats
     */
    const ERROR_UNSUPPORTED_ARCHIVE_TYPE = 'unsupportedArchiveType';

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
    protected $archive_type;

    /**
     * Possible options this command may have
     *
     * @var mixed
     */
    protected $allowed_options = array(
        'rev', 'prefix', 'excluding', 'including', 'verbose', 'quiet'
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
        'destination', 'type'
    );

    protected $_messages = array(
        'unsupportedArchiveType' => 'That type of archive is not supported'
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
    public function __construct($param)
    {
        $this->setRevision($param);
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
     * Sets type of archive format.
     *
     * Valid values are 'files', 'tar', 'bzip2', 'gzip', 'zip'
     *
     * @param string $type is the archive type
     * @return VersionControl_Hg_Command_Archive
     */
    public function with($type)
    {
        $this->setArchiveType($type);

        /* for the fluent api */
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see VersionControl/Hg/Command/VersionControl_Hg_Command_Interface#execute($params)
     */
    public function execute(array $options)
    {
        //$global_options = $this->getGlobalOptions(); //implemented in parent
        //$options = array_merge($options, $global_options);
        if ( is_array($options)) {
            $this->addOptions($options);
        } elseif ( is_string($options) ) {
            //we want only a scalar and not an object nor a null
            $this->addOption($options);
        }

        $this->addOptions(array(
            'rev' => $this->getRevision(),
            'type' => $this->getArchiveType(),
            'repository' => $this->container->getRepository(),
        ));

        $destination = $this->getDestination() .
            DIRECTORY_SEPARATOR .
            '%b-r%R.' .
            $this->options['type'];

        /* If files: destination must not already exist! Else, Hg will report "Permission denied" */
        if (is_file($destination) || is_dir($destination) ) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                'The destination directory already exists, but it should not'
            );
        }
        /* if Not 'files', then it has to be a filename! */

        $modifiers = null;
        foreach ($this->getOptions() as $option => $argument) {
            $modifiers .= ' --' . $option . ' ' . $argument;
        }

        $command_string = '"'.$this->container->hg->getHgExecutable().'" ' . $this->command;
        /* rtrim() instead of trim() so we don't end up with no space before
         * first switch: "hg archive--rev tip" */
        $command_string .= rtrim($modifiers);
        $command_string .= " $destination";

var_dump($params);
echo "\r\n";
var_dump($command_string);

        exec($command_string, $output, $command_status);
        //@todo remove the die()...
        ($command_status === 0) or
            die("returned an error: " . var_dump($command_status));
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

    /**
     * Sets the directory path to which the archive will be saved
     *
     * @param string $directory
     *
     * @return void
     */
    public function setDestination($directory)
    {
        if ( empty($directory) ) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                'I was not told where the archive should go to'
            );
        }

        $this->destination = $directory;
    }

    /**
     * Gets the directory path to which the archive will be saved
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Sets the change set revision to archive
     *
     * @param $revision
     *
     * @return void
     *
     * @todo move this function to VersionControl_Hg_Command_Archive
     */
    public function setRevision($revision = 'tip')
    {
        $this->revision = $revision;
    }

    /**
     * Gets the change set revision to archive
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }
}
