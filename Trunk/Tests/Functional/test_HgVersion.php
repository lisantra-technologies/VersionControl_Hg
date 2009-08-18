<?php

include_once '../../VersionControl/Hg.php';

$hg = new VersionControl_Hg();

/* explicit accessor */
var_dump($hg->getVersion());

/* verbose for command related to Hg executable */
var_dump($hg->version(array('verbose' => null)));

/* use quiet option for command related to Hg executable */
var_dump($hg->version(array('quiet' => null)));

/* misspelled option */
var_dump($hg->version(array('quite' => null)));