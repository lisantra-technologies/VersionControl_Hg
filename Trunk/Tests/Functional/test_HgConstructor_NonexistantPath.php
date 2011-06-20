<?php

include_once '../../VersionControl/Hg.php';

/* construct with an non-existing path */
echo "construct with an non-existing path\r\n";
try {
    $hg = new VersionControl_Hg("C:\Temp");
    var_dump($hg);
}
catch (Exception $e) {
    var_dump($e->getMessage());
}
