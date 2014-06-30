<?php
	namespace RealmServer\Config;

	#Database server adress
	define('MYSQL_HOST', 'localhost');
	#Database username
	define('MYSQL_USERNAME', 'root');
	#Database password
	define('MYSQL_PASSWORD', NULL);
	#Realm database name
	define('DB_REALM', 'atome');
	#Server database name
	define('DB_SERVER', 'atome');
	#World database name
	define('DB_WORLD', 'atome');
	#Game port (port number to listen)
	define('REALM_PORT', 443);
	#Activate debug mode ? (TRUE = yes | FALSE = no)
	define('DEBUG', TRUE);
	#Client version
	define('CLIENT_VERSION', '1.29.1');
	#Enable subscription ? (TRUE = yes | FALSE = no)
	define('ENABLE_SUBSCRIPTION', TRUE);
	#Maximum number of simultaneous clients allowed
	define('MAX_PLAYERS', 200);
	#Timezone
	ini_set('date.timezone', 'Africa/Algiers');
?>
