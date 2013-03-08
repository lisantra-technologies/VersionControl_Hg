<?php

include_once '../../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

/* explicit accessor */
//var_dump($hg->getVersion());

var_dump($hg->version()->run(array('verbose' => null)));
var_dump($hg->version()->run('verbose'));

/* getting the property */
//echo $hg->version;

/* misspelled option */
var_dump($hg->version()->run(array('quite' => null)));
