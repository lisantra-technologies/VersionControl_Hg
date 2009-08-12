<?php


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
class Hg_Repository_Command extends Hg_Command
{

}

