<?php

include_once '../../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');


var_dump($hg->version()->run(array('verbose' => null)));
var_dump($hg->version()->run('verbose'));

//@TODO 'major' arugments not yet implemented
var_dump($hg->version('raw')->run('verbose'));
var_dump($hg->version('major')->run('verbose'));
var_dump($hg->version('minor')->run('verbose'));
var_dump($hg->version('raw')->run('verbose'));

/* getting the property */
//This currently does not / cannot (?) work since it only
//echo $hg->version;

//property can only be gotten through the executable
echo $hg->executable->version;
