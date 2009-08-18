<?php

include_once '../VersionControl/Hg.php';

//var_dump($_SERVER); die;

$repo = realpath('Fixtures/Test_Repository');
$hg = new VersionControl_Hg($repo);
echo "getting the repository path\r\n";
var_dump($hg->getRepository());
echo "\r\n";
$hg->archive('tip', realpath('../'), 'gzip');

/*
$hg = new VersionControl_Hg();
$hg->setRepository('Fixtures/Test_Repository');
$hg->archive('tip')->to('/home/myself/releases/')->with('gzip');

$hg = new VersionControl_Hg();
$hg->setRepository('Fixtures/Test_Repository')->archive('tip')->to('/home/myself/releases/')->with('gzip');


$hg = new VersionControl_Hg();
$hg->use('Fixtures/Test_Repository')->archive('tip')->to('/home/myself/releases/')->with('gzip');
*/