<?php

include_once '../../VersionControl/Hg.php';

/* construct with an invalid repository */
echo "construct with an invalid repository\r\n";
try {
    $repo = realpath("C:\Windows\Temp");
    $hg = new VersionControl_Hg($repo);
    var_dump($hg);
}
catch (Exception $e) {
    var_dump(get_class($e), $e->getMessage());
}
