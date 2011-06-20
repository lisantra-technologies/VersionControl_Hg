<?php

include_once '../../VersionControl/Hg.php';

$cloned_repository = 'H:\Development\_Webroot\Trunk\Tests\Fixtures\Clone_of_Repository';
$from_repository = 'H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository' ;

$hg = new VersionControl_Hg($cloned_repository);

$pulled_changesets = $hg->pull()->from($from_repository)->run();

var_dump($pulled_changesets);
die('end of test');
