<?php
Class Config
{
	public $sql_host, $sql_username, $sql_password, $realm_database, $game_database, $world_database, $realm_port, $debug, $client_version,$subscription,$max_players;

	public function __construct(){

		$this->sql_host = "localhost";
		$this->sql_username = "root";
		$this->sql_password = ""; 
		$this->realm_database = "atome_realm";
		$this->game_database = "";
		$this->world_database = "";
		$this->realm_port = 443;
		$this->debug = true;
		$this->client_version ="1.29.1";
		$this->subscription =  false;
		$this->max_players = 200;

	}
}

$Config = New Config;
