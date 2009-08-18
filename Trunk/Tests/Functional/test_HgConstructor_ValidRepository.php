<?php

/* construct with a valid repository */
echo "construct with a valid repository\r\n";
$repo = realpath('V:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());
