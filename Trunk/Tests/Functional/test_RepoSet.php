<?php

include_once '../../VersionControl/Hg.php';

/* 'Normal' way*/
$repo = realpath('../Fixtures/Test_Repository');
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());

$hg = null;
unset($hg);

/* Should fail */
$repo = realpath('../Fixtures/Test_Repository');
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());
$hg->setRepository(realpath('../Fixtures/Test_Repository2'));
var_dump($hg->getRepository());

$hg = null;
unset($hg);

/* Should succeed! */
$repo = realpath('../Fixtures/Test_Repository');
$hg = new VersionControl_Hg($repo);
var_dump($hg->getRepository());
/* secret trick! */
VersionControl_Hg_Container_Repository::reset();
$hg->setRepository(realpath('../Fixtures/Test_Repository2'));
var_dump($hg->getRepository());

$hg = null;
unset($hg);

/* Excplicitly call setPath() */
$hg = new VersionControl_Hg();
$hg->repository->setPath(realpath('../Fixtures/Test_Repository'));
var_dump($hg->getRepository());

$hg = null;
unset($hg);

/* set the repository as a property */
$hg = new VersionControl_Hg();
$hg->repository = realpath('../Fixtures/Test_Repository');
var_dump($hg->getRepository());
//var_dump($hg->repository->path);
var_dump($hg->repository);

$hg = null;
unset($hg);
