<?php

include_once '../../VersionControl/Hg.php';

$hg = new VersionControl_Hg();

/* using default executable found on system path */
var_dump($hg->getExecutable());

die;


/* invalid executable */
try {
    $hg->setExecutable("C:\Documents and Settings\mgatto\My Documents");
    var_dump($hg->getExecutable());
}
catch (Exception $e) {
    var_dump($e->getMessage());
}

/* non-existant path */
try {
    $hg->setExecutable("C:\Mercurial");
    var_dump($hg->getExecutable());
}
catch (Exception $e) {
    var_dump($e->getMessage());
}

/* valid, alternative executable */
$hg->setExecutable("C:\Program Files\TortoiseHg");
var_dump($hg->getExecutable());
