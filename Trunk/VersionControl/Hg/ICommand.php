<?php

interface VersionControl_Hg_ICommand
{
    public function __construct(array $options);
    public function execute();
}