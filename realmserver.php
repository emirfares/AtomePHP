<?php

	error_reporting(E_ALL);
	$start = memory_get_usage();
	require_once('RealmServer/config.php');
	require_once('Extra/functions.php');
	require_once('RealmServer/Utils/Utils.php');
	require_once('RealmServer/Sql/database.php');

	require_once('RealmServer/Realm/realmclient.php');
	require_once('RealmServer/Server/gameservers.php');

	
	$end = convert(memory_get_usage() - $start);
	print_info("Used Memory : {$end}") ;
	require_once('RealmServer/Server/listner.php');
?>