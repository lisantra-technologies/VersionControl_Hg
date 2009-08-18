<?php

include_once '../../VersionControl/Hg.php';

$base_path = "../../Tests/Functional";

$includes = glob($base_path . DIRECTORY_SEPARATOR . "test_HgConstructor_*.php");

foreach ($includes as $test) {
    xdebug_start_trace('../../Documentation/Benchmarks/' . basename($test) . '_trace'); //2nd param = 1 for append mode
        include $test;
    xdebug_stop_trace();
}
