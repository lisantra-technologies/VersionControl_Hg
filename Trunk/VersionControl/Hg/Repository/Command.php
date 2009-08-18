<?php

require_once 'Exception.php';

/**
 * Wraps each Mercurial command to centralize global options and command execution.
 *
 * It is passed into the constructor of each command.
 *
 * implements the following global options:
 * -I --include    include names matching the given patterns
 * -X --exclude    exclude names matching the given patterns
 *
 * @package VersionControl_Hg
 * @subpackage Commands
 */
class VersionControl_Hg_Repository_Command extends VersionControl_Hg_Command
{
    /**
     * Implemented commands pertaining to a Hg repository
     *
     * @var mixed
     */
    protected $allowed_commands = array(
        'archive', 'changelog', 'status',
    );

    /**
     * Holds the repository object needed by commands to operate upon
     *
     * @var VersionControl_Hg_Repository
     */
    protected $container;

    /**
     *
     * @param   VersionControl_Hg_Repository $repository
     * @return  void
     */
    public function __construct(VersionControl_Hg_Repository $repository)
    {
        $this->container = $repository;
    }

    /**
     * Executes the actual mercurial command
     *
     * @param   string $method
     * @param   mixed $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
        if ( ! in_array($method, $this->allowed_commands)) {
            throw new VersionControl_Hg_Repository_Command_Exception(
                'Command not implemented or not a part of Mercurial'
            );
        }

        //abstracted as $container so child command classes can inheret it.
        $class = get_class($this->container) . '_Command_' . ucfirst($method);

        include_once "Command/" . ucfirst($method) . ".php";

        if ( ! class_exists($class)) {
            throw new VersionControl_Hg_Exception(
                "Sorry, The command \'{$method}\' is not implemented"
            );
        }

        $options = $args;

        //$args is an array wrapped around the option; simplify
        /*if (count($args > 0)) {
            $options = $args[0];
        }*/
        //instantiate the class implementing the command
        $command_class = new $class($this->container);

//@todo if I test for if the method really exists maybe I can do the fluent API!
        if (method_exists($command_class, $method)) {
            return $command_class->$method($options);
        }

        //run its execute method
        return $command_class->execute($options);
    }
}
