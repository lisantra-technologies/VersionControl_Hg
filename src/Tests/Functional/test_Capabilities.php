<?php

include_once '../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

//Most basic of tests:
var_dump($hg->getExecutable()->hasCapability('filesets'));
var_dump($hg->getExecutable()->hasCapability('revsets'));
var_dump($hg->getExecutable()->hasCapability('subrepos:git'));
var_dump($hg->getExecutable()->hasCapability('subrepos:bzr'));

die;
