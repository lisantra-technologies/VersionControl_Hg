<?php



class Hg_Repository_Command_Init
    extends Hg_Repository_Command implements Hg_Command_ICommand
{
    private $_options = array(
        'path' => null,
    );

    private $_hg_command = 'init';

    //no options for 'init'
    public function __construct(array $options = null)
    {

    }

    //terminal method in the chain; its a setter
    //$repo->create()->at()
    public function at($path)
    {
        if ( empty($path) ) {
            throw new VersionControl_Hg_Exception(

            );
        }

        if ($this->isRepository($path)) {
            throw new VersionControl_Hg_Exception(
                'I will not let you overwrite an existing repo'
            );
        }

        $this->_options['path'] = $path;
        $this->execute();
    }

    public function execute()
    {
        exec(
            escapeshellcmd($this->getHgExecutable.' '.$this->_hg_command) .
            ' ' .
            escapeshellarg($this->_options['path']),
            $output
        );
    }

}
