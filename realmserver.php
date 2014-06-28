<?php
	define('DEBUG', true);
	ini_set('date.timezone', 'Europe/Paris');
	
	# memory function
	function convert($size)
	{
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
	}

	$start = memory_get_usage();
	$start_time = microtime(true);

	# Require all files
	require_once('src/RealmServer/config.php');
	require_once('src/Ext/Bilbon/ORM/bilbon.php');
	require_once('src/Console/Logs.php');

	# Setup Logs
	use Console\Logs as Logs;

	Logs::init_logs();
	Logs::print_log('launch', 'Loading all files...');
	# End

	require_once('src/Ext/Crypt/Random.php');
	require_once('src/Ext/Crypt/Crypt.php');

	require_once('src/Objects/Account.php');
	require_once('src/Objects/Server.php');
	require_once('src/RealmServer/Realm/RealmClient.php');
	require_once('src/RealmServer/Server/GameServer.php');
	# End

	$end_time = microtime(true);
	$time = number_format($end_time - $start_time, 3);
	$end = convert(memory_get_usage() - $start);

	Logs::print_log('launch', "Files loaded ! [{$end}, {$time} s]");

	require_once('src/RealmServer/Main/Client.php');

	use RealmServer\Main\Client as Client;

	$clients = array();
	
	try 
	{
		$server = socket_create_listen($config->realm_port);
		Logs::print_log('launch', "Server listening clients on port {$config->realm_port}");
	}
	catch (Exception $e)
	{
		Logs::print_log('error', "Can't start listener on port {$config->realm_port}");
	}
		
	while(($client = socket_accept($server)))
	{
		$clients[] = new Client($client);
	}
?>