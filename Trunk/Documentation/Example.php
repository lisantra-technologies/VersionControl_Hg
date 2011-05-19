<?php

include 'VersionControl/Hg.php';

$repo = new VersionControl_Hg('V:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');
//--OR--
$repo = new VersionControl_Hg();
$repo->setRepository('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');
$path = dir('H:\Development\_Webroot\Trunk\Tests\Fixtures\Test_Repository');
$repo->setRepository($path);

//------------------------------------------
$repo->export(HG::ALL)->to('/home/myself/releases/')->as(HG::ZIP);
//------------------------------------------

$destination = dir('/path/'); //for type = files

$repo->export($files)->to($destination);

//---------------------------------------

$hg = new VersionControl_Hg('H:\repo');
$unversioned_files = $hg->getStatus(HG::UNVERSIONED);

//---------------------------------------

//set the working files to be acted upon
$files = new FileCollection( array(
                        'location' => new Directory( '/path/' ),
                        'exclude' => '',
                        'include' => '',
));


//---------------------------------------
hg_archive(    )



/*
 switches: -r = revision | range of revisions: array(22,30)

 add	params( file | directory ); options:( -I include files matching patter )
 annotate  blame
 archive -p:(directory prefix) -r:(rev number) -t:(files, tar, tbz2, tgz, uzip, zip) -I X
 clone
 commit ci
 diff
 export
 init
 log  history
 merge
 parents
 pull
 push
 remove
 revert
 serve
 status
 update, co, checkout
*/
 ?>