<?php

	namespace RealmServer\Server;

	use Objects\Server as Server;
	use Ext\Bilbon\ORM;
	use PDO;

	class GameServer
	{
		public $server_list = array();
		public static $db_realm;

		function __construct()
		{
			global $config;

			ORM\Storage::$instances['realm'] = array(
		    	'dsn' => 'mysql:host='.$config->mysql_informations['host'].';dbname='.$config->mysql_informations['db_realm'],
		    	'user' => $config->mysql_informations['username'],
		    	'password' => $config->mysql_informations['password']
			);

			static::$db_realm = ORM\Storage::get('realm');
			
			$this->loadServers();
		}

		private function loadServers()
		{
			$qGameserver = static::$db_realm->query('SELECT * FROM gameservers');
			$server = new Server($qGameserver->fetch(PDO::FETCH_ASSOC), static::$db_realm);
			$server->__set('state', 0);
			array_push($this->server_list,$server);
			//$this->server_list[$server->__get('id')] = $server;
		}

		public function serverExists($id)
		{
			foreach ($this->server_list as $server_id => $server)
			{
				if ($server_id == $id)
				{
					return true;
				}
			}

			return false;
		}

		public function getServer($id)
		{
			foreach ($this->server_list as $server_id => $server)
			{
				if ($server_id == $id)
				{
					return $server;
				}
			}

			return null;
		}

		public function parsePacket()
		{
			$packet = null;
			$first = true;

			foreach($this->server_list as $server)
			{
				($first) ? $first = false : $packet .= "|";
				$packet .= $server->__get('id').';'.$server->__get('state').';'.(75 * $server->__get('id')).';1';
			}
			return $packet;
		}
	}

	$game_servers = new GameServer;

?>