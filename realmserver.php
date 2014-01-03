<?php
error_reporting(E_ALL^E_STRICT);
$start = memory_get_usage();

//require_once('Extra/multithreading.php');
require_once('RealmServer/config.php');
require_once('Extra/functions.php');
require_once('RealmServer/Sql/database.php');


require_once('RealmServer/Realm/realmclient.php');
require_once('RealmServer/Utils/Utils.php');
$end = convert(memory_get_usage() - $start);

require_once('RealmServer/Server/gameservers.php');
print_info("Used Memory : {$end}\n") ;
require_once('RealmServer/Server/tcp_listner.php');
