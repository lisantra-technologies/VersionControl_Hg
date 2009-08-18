<?php

include_once '../../VersionControl/Hg.php';

/* construct with no repository */
$hg = new VersionControl_Hg();
var_dump($hg->getRepository());

/* construct with a valid repository */
$repo = realpath('../Fixtures/Test_Repository');
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());

/* construct with an invalid repository */
$repo = realpath("C:\Windows\Temp");
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());

/* construct with an non-existing path */
$repo = realpath("C:\Temp");
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());
