<?php

/* construct with an invalid repository */
echo "construct with an invalid repository\r\n";
try {
    $repo = realpath("C:\Windows\Temp");
    $hg = new VersionControl_Hg($repo);
    var_dump($hg->getRepository());
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
