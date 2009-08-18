<?php

include_once '../VersionControl/Hg.php';

//var_dump($_SERVER); die;

$hg = new VersionControl_Hg();
echo $hg->getHgExecutable();
echo $hg->getVersion();
echo $hg->version(array('verbose' => null));
