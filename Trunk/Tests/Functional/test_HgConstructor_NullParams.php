<?php

/* Construct with no parameters */
echo "construct with no repository\r\n";
$hg = new VersionControl_Hg();
var_dump($hg->getRepository());
