<?php

/*
 * Test with safe_mode enabled!
 * Note: When safe mode is enabled, you can only execute files within the safe_mode_exec_dir. For practical reasons, it is currently not allowed to have .. components in the path to the executable.
 * With safe mode enabled, the command string is escaped with escapeshellcmd(). Thus, echo y | echo x becomes echo y \| echo x.
 */
ini_set('safe_mode', 1);

include_once '../VersionControl/Hg.php';
$hg = new VersionControl_Hg('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');

//var_dump($hg->status()->all()->run('verbose'));

//var_dump($hg->status(array('removed', 'deleted'))->run('verbose'));
//var_dump($hg->status()->removed()->deleted()->run('verbose'));

/*
 * for the syntax of a single arg for the base command function, it must be
 * the name of a function within the command, and not need a value. In this case,
 * 'all' is a solitary modifier represented by array('all' => null) in Status.php */
var_dump($hg->status('all')->revision('2')->format('raw')->run('verbose'));

var_dump($hg->status()->all()->revision('2')->run('verbose'));

var_dump($hg->status('all')->files(array('index.php'))->run());

/* without 'all', result is empty EVEN though there are items which should display  */
var_dump($hg->status()->files(array('index.php'))->run());

/* In this case, 'excluding' did not work and we did indeed get 'all' files,
 * including index.php */
var_dump($hg->status()->all()->excluding('**.php')->run());

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
