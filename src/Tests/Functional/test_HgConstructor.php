<?php

include_once '../../VersionControl/Hg.php';
$base_path = "./";

$includes = glob($base_path . DIRECTORY_SEPARATOR . "test_HgConstructor_*.php");

foreach ($includes as $test) {
    include $test; //code runs automatically
}
