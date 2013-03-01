<?php

include_once '../VersionControl/Hg.php';

$hg = new VersionControl_Hg();

/* Rely on Init.php to do work? */
$repository = $hg->init('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository2')->run();

echo $repository;
var_dump($repository);
