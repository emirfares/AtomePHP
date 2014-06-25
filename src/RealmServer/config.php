<?php
	namespace RealmServer\Config;

	class Config
	{
		public $sql_informations,
			   $realm_port, 
			   $debug, 
			   $client_version,
			   $enable_subscription,
			   $max_players;

		public function __construct()
		{
			$this->mysql_informations = array(
				'host' => 'localhost',
				'username' => 'root',
				'password' => null,
				'db_server' => 'atome',
				'db_realm' => 'atome',
				'db_world' => 'atome',
			);

			$this->realm_port = 443;
			$this->debug = true;
			$this->client_version = "1.29.1";
			$this->enable_subscription = false;
			$this->max_players = 200;
		}
	}

	$config = new Config;

?>
