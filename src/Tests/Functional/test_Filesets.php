<?php

include_once '../VersionControl/Hg.php';
$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

/* Displays index.php */
//var_dump($hg->status('all')->excluding("**.php")->run());

//var_dump($hg->status('all')->excluding("glob:**.php")->run());

//var_dump($hg->status('all')->excluding(array('glob' => '**.php'))->run());

//var_dump($hg->status()->files(array('glob' => '**.php'))->run());

var_dump($hg->status('all')->files(array('index.php'))->run());

//var_dump($hg->status()->files("glob:**.php")->run());

//var_dump($hg->status()->files(array('glob' => '**.php'))->run());

//var_dump($hg->status('all')->excluding("awesome:**.php and not encoding(ascii)")->run());

//var_dump($hg->status('all')->excluding(array('set' => "**.php and encoding(ascii)"))->run());
