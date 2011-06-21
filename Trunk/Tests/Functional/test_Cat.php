<?php

include_once '../VersionControl/Hg.php';

$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

//var_dump($hg->cat('index.php')->run());


var_dump($hg->cat('data.xml')->save()->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->run());
die;

$hg->cat(array('index.php', 'layout.html'))->save()->to(realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures'))->run();

$hg = new VersionControl_Hg('/path/to/repo');
$hg->cat('/path/to/a/file')->run();


//You may also specify multiple files:

$hg = new VersionControl_Hg('/path/to/repo');
$hg->cat(array('file1', 'file2'))->run();


//Additionaly, you may cat the contents of a file at a specific revision:

$hg = new VersionControl_Hg('/path/to/repo');
$contents = $hg->cat('file2')->revision(6)->run();
file_put_contents('file2', $content);

//Not specifying a revision causes Mercurial to cat the latest version of the
//file.

//As a convenience for the latter operation, a programmer may use the save()
//method:

$hg = new VersionControl_Hg('/path/to/repo');
$hg->cat('file2')->revision(6)->save('new_file_name')->to('/path')->run();


//or, spell out the options in an array:

$hg->cat('file2')->save(array('name' => 'new_file_name', 'to' => '/path'))->run();
