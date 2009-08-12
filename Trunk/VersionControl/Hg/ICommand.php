<?php

interface Hg_Command_ICommand
{
    public function __construct(array $options);
    public function execute();
}