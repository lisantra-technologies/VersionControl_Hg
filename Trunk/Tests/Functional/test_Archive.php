<?php

include_once '../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

$hg->archive()->revision('tip')->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->with('zip')->run();

$hg->archive(array('revision' => 'tip', 'to' => realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures')))->with('gzip')->run();

$hg->archive(array('revision' => 'tip', 'to' => realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'), 'with' => 'bzip2'))->run();

/*
$hg->connect()->to('127.0.0.1')->by('ssh')->using('username', 'password')->run();
$hg->archive('tip')->to('/home/myself/releases/')->with('gzip');
*/
