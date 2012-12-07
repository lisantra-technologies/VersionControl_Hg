<?php

include_once '../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

$hg->archive('tip')->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->with('zip')->run();
$hg->archive(3)->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->with('zip')->run();
$hg->archive()->revision('tip')->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->with('zip')->run();
$hg->archive(array('revision' => 'tip', 'to' => realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures')))->with('gzip')->run();
$hg->archive(array('revision' => 'tip', 'to' => realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'), 'with' => 'bzip2'))->run();
