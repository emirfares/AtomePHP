<?php
Class DatabaseConnection{
	static public function Connect ($dbname){
		global $config;
		switch ($dbname){
			case "realm":
				$dbname = $config->realm_database;
			break;

			case "server":
				$dbname = $config->server_database;
			break;

			case "world":
				$dbname = $config->world_database;
			break;
		}
	 return New PDO("mysql:host={$config->sql_host};dbname=".$dbname,$config->sql_username,$config->sql_password);
	}
}