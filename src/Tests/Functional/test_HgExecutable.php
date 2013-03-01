<?php

include_once '../../VersionControl/Hg.php';

$hg = new VersionControl_Hg();

/* using default executable found on system path */
var_dump($hg->getHgExecutable());

/* invalid executable */
try {
    $hg->setHgExecutable("C:\Documents and Settings\mgatto\My Documents");
    var_dump($hg->getHgExecutable());
}
catch (Exception $e) {
    var_dump($e->getMessage());
}

/* non-existant path */
try {
    $hg->setHgExecutable("C:\Mercurial");
    var_dump($hg->getHgExecutable());
}
catch (Exception $e) {
    var_dump($e->getMessage());
}

/* valid, alternative executable */
$hg->setHgExecutable("C:\Program Files\Mercurial");
var_dump($hg->getHgExecutable());