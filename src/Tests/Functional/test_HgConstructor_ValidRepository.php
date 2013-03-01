<?php
//xdebug_start_trace('./trace.log');

include_once '../../VersionControl/Hg.php';

/* construct with a valid repository */
echo "construct with a valid repository\r\n";
$repo = realpath('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');
$hg = new VersionControl_Hg($repo);

echo $hg;

//var_dump($hg, $hg->repository, $hg->repository->getPath(), $hg->getRepository(), $hg->getRepository()->getPath());

//xdebug_stop_trace();
