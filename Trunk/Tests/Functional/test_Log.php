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

die;

/* without 'all', result is empty EVEN though there are items which should display  */
var_dump($hg->status()->files(array('index.php'))->run());

/* In this case, we left out 'all' which correctly excludes index.php */
var_dump($hg->status()->excluding('**.php')->run());

/* this displays nothing! Seems to be by design */
var_dump($hg->status()->including('**.php')->run());

/* Displays index.php */
var_dump($hg->status()->all()->including('**.php')->run());

/* Displays index.php */
var_dump($hg->status('all')->files(array('index.php'))->excluding('**.php')->run());

/* Returns nothing! */
var_dump($hg->status()->files(array('index.php'))->excluding('**.php')->run());

/*
 * In a case like the following:
 * <code>$hg->status('all')->files(array('index.php'))->excluding('**.php')->run();</code>
 * The apparently conflicting options do not cause an exception. The behavior is that
 * apparently, files, takes complete precedence over excluding. However, all such
 * combinations have not been fully tested yet, so you may not get the results
 * you expect by using options which conceptually conflict with each other.
 */


/*
 * Future concepts, but 'asXml' violates my no camel case API design principle
var_dump($hg->status('all')->asXml()->run('verbose'));
var_dump($hg->status('all')->toXml()->run('verbose'));
var_dump($hg->status('all')->xml()->run('verbose'));

'at' could be confused with a time/date
var_dump($hg->status('all')->at('22')->run('verbose'));
*/
