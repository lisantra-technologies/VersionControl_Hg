<?php

/*
 * Test with safe_mode enabled!
 * Note: When safe mode is enabled, you can only execute files within the safe_mode_exec_dir. For practical reasons, it is currently not allowed to have .. components in the path to the executable.
 * With safe mode enabled, the command string is escaped with escapeshellcmd(). Thus, echo y | echo x becomes echo y \| echo x.
 */
ini_set('safe_mode', 1);

include_once '../VersionControl/Hg.php';
$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

echo "ALL\r\n";
var_dump($hg->log()->run('verbose'));

echo "DATES\r\n";
var_dump($hg->log()->on('Dec 27, 2010')->run());
var_dump($hg->log()->before('Dec 27, 2010')->run());
var_dump($hg->log()->after('Dec 27, 2010')->run());
var_dump($hg->log()->between('Dec 27, 2010', '2010-12-31')->run());

echo "REVISIONS: 2\r\n";
var_dump($hg->log()->revision('2')->format('raw')->run('verbose'));
var_dump($hg->log()->revision('2')->run('verbose'));

echo "FILES: ONLY INDEX.PHP\r\n";
var_dump($hg->log()->files(array('index.php'))->run());

echo "EXCLUDING PHP FILES\r\n";
var_dump($hg->log()->excluding('**.php')->run());
/* Displays nothing; excluding takes precedence. Remove excluding() to get only index.php */
var_dump($hg->log()->files(array('index.php'))->excluding('**.php')->run());

echo "INCLUDING PHP FILES\r\n";
//@TODO Add another PHP file to the repo to test for sure
var_dump($hg->log()->including('**.php')->run());
