<?php

namespace RealmServer\Main;

use Ext\Bilbon\ORM as ORM;
use Ext\Crypt as Crypt;

use Console\Logs as Logs;
use RealmServer\Realm as Realm;
use Worker;

class Client extends Worker 
{
	public static $db_realm;
	public $key;
	public $state;
	public $account = array();

	public function __construct($socket)
	{
		global $config;

		$this->__set('config', $config);
		$this->__set('socket', $socket);
		$this->__set('key', Crypt\Random::generateStr());
		socket_getpeername($this->socket, $ip);
		$this->__set('ip', $ip);
		$this->__set('hostname', gethostbyaddr($this->ip));

		$this->start(PTHREADS_INHERIT_ALL | PTHREADS_ALLOW_GLOBALS);
	}

    public function __get($property) 
    {
        return isset($this->$property) ? $this->$property : null;
    }
    
    public function __set($property, $value) 
    {
        $this->$property = $value;
    }

	public function send($buffer)
	{
		socket_write($this->socket, $buffer.chr(0));
		Logs::print_log('debug', "Socket send {$buffer}", false);
	}

	public function run()
	{
		$this->__set('state', 'version_checking');
		Logs::print_log('debug', "New client added ({$this->key}), version checking...", false);
		$this->send('HC'.$this->key);
		$buff = null;

		do 
		{
			$buffer = socket_read($this->socket, 1024);
				
			if (empty($buffer))
			{
				$this->disconnect();
			}

			$buffer = (str_replace(chr(10), null, $buffer));

			for ($i = 0; $i < strlen($buffer); $i++) 
			{ 
			    if ($buffer[$i] != chr(0)) 
			 		$buff .= $buffer[$i];
			    else
			    {
			    	Logs::print_log('debug', "Socket recieved {$buff}", false);
			    	$this->parsePacket(trim($buff));
			    	$buff = null;
			    }  	
			}
		} 

		while($buffer != null);
	}

	private function parsePacket($packet)
	{
		switch($this->__get('state'))
		{
			case 'version_checking' :
				if ($packet == $this->config->client_version) 
				{
					$this->__set('state', 'account_checking');
					Logs::print_log('debug', 'Version validated, account verification...', false);
				}
				else 
				{
					$this->send('AlEv'.$this->config->client_version);
					Logs::print_log('debug', "Wrong version ({$packet}) form client {$this->key}", true);
					$this->__set('state', null);
					$this->disconnect();
				}
			break;

			case 'account_checking':
				$this->parseAccount($packet);
			break;

			case 'base':
				$this->parseBase($packet);
			break;
		}
	}

	private function parseAccount($packet) 
	{
		global $game_servers;
		global $config;

		ORM\Storage::$instances['realm'] = array(
	    	'dsn' => 'mysql:host='.$config->mysql_informations['host'].';dbname='.$config->mysql_informations['db_realm'],
	    	'user' => $config->mysql_informations['username'],
	    	'password' => $config->mysql_informations['password']
		);

		self::$db_realm = ORM\Storage::get('realm');

		$pack = explode('#', $packet);
		$username = strtolower($pack[0]);

		$account = new Realm\RealmClient($username);
		$account = $account->account;

		if ($account != null)
		{
			$tpass = trim(Crypt\Crypt::cryptPassword($account->__get('password'), $this->key));

			if ($username == strtolower($account->__get('username')) && $tpass == trim($pack[1])) 
			{
				if (!$account->__get('is_banned'))
				{
					$this->account = $account->__getAll();

					$this->send('Ad'.$account->__get('nickname'));
					$this->send('Ac0');
					$this->send('AH'.$game_servers->parsePacket());
					$this->send(($account->__get('gmlevel') > 0) ? 'Alk1' : 'Alk0');
					$this->send('AQ');

					$this->__set('state', 'base');
					Logs::print_log('debug', "Client {$account->__get('nickname')} connected !", true);
				}
				else
				{
					$this->send('AlEb');
					$this->__set('state', null);
					Logs::print_log('debug', "Client {$account->__get('nickname')} is banned, connection refused !", true);
				}
			}
			else 
			{
				$this->__set('state', null);
				$this->send('AlEf');
			}
		}
		else
		{
			$this->__set('state', null);
			$this->send('AlEf');
		}
	}

		/*!	@function	sendCharactersList
			@abstract	Sends remaining subscription time and the list of client's characters.
			@param		string - Packet receved.
			@result		void
		*/
		private function sendCharactersList() 
		{
			global $game_servers;

			$time = null;

			if ($this->config->enable_subscription)
			{
				$time = 0; 
			}
			else
			{
				$time = (365 * 24 * 3600) * 1000; // TODO : Parse subscription time from the database
			}

			$packet = 'AxK'.$time;

			foreach ($game_servers->server_list as $server)
			{
				if (array_key_exists($server->__get('id'), $this->account['characters']))
				{
					$packet .= '|'.$server->__get('id').','.count($this->account['characters'][$server->__get('id')]);
				}
			}

			$this->send($packet);	
		}

		/*!	@function	parseCommand
			@abstract	Parses admin commands sent using the game admin console.
			@param		string - Packet receved.
			@result		void
		*/
		private function parseCommand($packet)
		{
			$p = explode(" ",$packet);
			switch (strtolower($p[0])) 
			{
				case 'packet':
					$this->send($p[1]);
				break;
			
				default:
					$this->send('BAE');
				break;
			}
		}

		/*!	@function	parseGameServer
			@abstract	Parses game server redirection request.
			@param		integer - server ID.
			@result		void
		*/
		private function parseGameServer($id)
		{

		}

		/*!	@function	parseBase
			@abstract	Parses Base packets  (Packets sent after successful login).
			@param		string - Packet receved.
			@result		void
		*/
		private function parseBase($packet)
		{
			switch (substr($packet, 0,2))
			{
				case 'Ax':
					$this->sendCharactersList();
				break;

				case 'AX':
					$this->parseGameServer(substr($packet, 2));
				break;

				case 'Af': //TODO : Position of the client in the queue 
					$this->send("Af1|0|0|1|-1");
				break;

				case 'BA':
					$this->parseCommand(substr($packet, 2));
				break;
			}
		}

		/*!	@function	disconnect
			@abstract	Disconnects the client.
			@result		void
		*/
		public function disconnect()
		{
			socket_close($this->socket);
			print_info('Client disconnected');
			$this->shutdown();
		}
	}
?>