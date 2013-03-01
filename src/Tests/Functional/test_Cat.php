<?php

include_once '../VersionControl/Hg.php';
$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

//Most basic of tests:
echo "Dumping index.php \r\n";
var_dump($hg->cat('index.php')->run());

//now, dump at a specific revision
/* @TODO This returned a curious error "Array: no such file in rev d7d081382001" */
echo "Dumping index.php at revision 1 \r\n";
var_dump($hg->cat('index.php')->revision(5)->run());

echo "cat two versions of the same file\r\n";
var_dump($hg->cat('index.php')->revision(4)->revision(0)->run());

//Now, dump with multiple files:
echo "Dumping two different files: index.php and then layout.html @ tip \r\n";
var_dump($hg->cat()->files(array('index.php', 'layout.html'))->run());

//Save the catted file
echo "Saving data.xml to Fixtures \r\n";
var_dump($hg->cat('data.xml')->save()->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->run());

echo "Save multiple files with their original names \r\n";
$hg->cat()->files(array('index.php', 'layout.html'))->save()->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->run();

echo "Using long options in save() \r\n";
$hg->cat('layout.html')->save(array('name' => 'index.phtml', 'to' => realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures')))->run();
