<?php
	namespace RealmServer\Config;

	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USERNAME', 'root');
	define('MYSQL_PASSWORD', null);
	define('DB_REALM', 'atome');
	define('DB_SERVER', 'atome');
	define('DB_WORLD', 'atome');
	define('REALM_PORT', 443);
	define('DEBUG', true);
	define('CLIENT_VERSION', '1.29.1');
	define('ENABLE_SUBSCRIPTION', false);
	define('MAX_PLAYERS', 200);

	ini_set('date.timezone', 'Europe/Paris');
?>
