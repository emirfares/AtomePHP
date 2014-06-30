<?php

	namespace RealmServer\Main;

	use Ext\Bilbon\ORM as ORM;
	use Ext\Crypt as Crypt;

	use Console\Logs as Logs;
	use RealmServer\Realm as Realm;
	use Thread;

	/**	@class		Client
	 *	@author		Emir Fares BELMAHDI & Jean Walrave
	 *	@abstract 	Handling new clients & parsing socket connections.
	 */
	class Client extends Thread 
	{
		/**	@var		dbrealm
		 *	@abstract	ressource - The client's PDO Connection.
		 */
		public static $db_realm;

		/**	@var		key
		 *	@abstract	string - The client's encryption key.
		 */
		public $key;

		/**	@var		state
		 *	@abstract	string - The client's packet state.
		 */
		public $state;

		/**	@var		infos
		 *	@abstract	ressource - The client's account informations.
		 */
		public $account = array();

		/**	@function	__construct
		 *	@abstract	Launches the Thread and start parsing packets.
		 *	@param		ressource - Socket ressource used to communicate with the client.
		 *	@return		void
		 */
		public function __construct($socket)
		{
			global $game_servers;

			$this->__set('game_servers', $game_servers);
			$this->__set('socket', $socket);
			$this->__set('key', Crypt\Random::generateKey(32));
			socket_getpeername($this->socket, $ip);
			$this->__set('ip', $ip);
			$this->__set('hostname', gethostbyaddr($this->ip));

			$this->start(PTHREADS_INHERIT_ALL);
		}

	    public function __get($property) 
	    {
	        return isset($this->$property) ? $this->$property : null;
	    }
	    
	    public function __set($property, $value) 
	    {
	        $this->$property = $value;
	    }

	    /**	@function	send
		 *	@abstract	Sends a packet to the client.
		 *	@param		string - Packet to send.
		 *	@return		void
		 */
		public function send($buffer)
		{
			socket_write($this->socket, $buffer.chr(0));
			Logs::print_log('debug', "Sent -> {$buffer}", false);
		}

		/**	@function	run
		 *	@abstract	Used by pThreads to run the new thread.
		 *	@return		void
		 */
		public function run()
		{
			$this->__set('state', 'version_checking');
			Logs::print_log('debug', "New client added ({$this->key}), checking version...", false);
			$this->send('HC'.$this->key);
			$buff = null;

			do 
			{
				$buffer = socket_read($this->socket, 1024);
					
				$buffer = (str_replace(chr(10), null, $buffer));

				for ($i = 0; $i < strlen($buffer); $i++) 
				{ 
				    if ($buffer[$i] != chr(0)) 
				 		$buff .= $buffer[$i];
				    else
				    {
				    	Logs::print_log('debug', "Recv <- {$buff}", false);
				    	$this->parsePacket(trim($buff));
				    	$buff = null;
				    }  	
				}
			}
			while($buffer != null);
			if (empty($buffer))
					$this->disconnect();
		}

		/**  @function	parsePacket
		 *	@abstract	Parses the packet receved from the client.
		 *	@param		string - Packet receved.
		 *	@return		void
		 */
		private function parsePacket($packet)
		{
			switch($this->__get('state'))
			{
				case 'version_checking' :
					if ($packet == CLIENT_VERSION) 
					{
						$this->__set('state', 'account_checking');
						Logs::print_log('debug', 'Version validated, account verification...', false);
					}
					else 
					{
						$this->send('AlEv'.CLIENT_VERSION);
						Logs::print_log('debug', "Wrong version ({$packet}) form client {$this->key} {$this->ip}");
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

		/**	@function	parseAccount
		 *	@abstract	Parses the account informations packet receved from the client.
		 *	@param		string - Packet receved.
		 *	@return		void
		 */
		private function parseAccount($packet) 
		{

			ORM\Storage::$instances['realm'] = array(
		    	'dsn' => 'mysql:host='.MYSQL_HOST.';dbname='.DB_REALM,
		    	'user' => MYSQL_USERNAME,
		    	'password' => MYSQL_PASSWORD
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
						$this->send('Ac0'); //TODO : Communities, Default (0) Francophone 
						$this->send('AH'.$this->game_servers->parsePacket());
						$this->send(($account->__get('gmlevel') > 0) ? 'Alk1' : 'Alk0');
						$this->send('AQ'.$account->__get('question'));

						$this->__set('state', 'base');
						//TODO : Disconnect other players from this account.
						Logs::print_log('debug', "Client {$account->__get('nickname')} connected !");
					}
					else
					{
						$this->send('AlEb');
						$this->__set('state', null);
						Logs::print_log('debug', "Client {$account->__get('nickname')} is banned, connection refused !");
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

		
		/**	@function	getSubscriptionTimeLeft
		 *	@abstract	returns remaining subscription time/
		 *	@return		long int - time left in milliseconde
		 */
		private function getSubscriptionTimeLeft ()
		{
			$timeLeft = (strtotime($this->account['subscriptionDate']) - strtotime(date('Y-m-d H:i:s')) );
			return ($timeLeft > 0) ? $timeLeft * 1000 : 0; 
		}

		/**	@function	sendCharactersList
		 *	@abstract	Sends remaining subscription time and the list of client's characters.
		 *	@return		void
		 */
		private function sendCharactersList() 
		{

			$time = null;

			(ENABLE_SUBSCRIPTION) ? $time = $this->getSubscriptionTimeLeft() : $time = 31536000000; //31536000000 = one year

			$packet = 'AxK'.$time;

			foreach ($this->game_servers->server_list as $server)
			{
				if (array_key_exists($server->__get('id'), $this->account['characters']))
				{
					$packet .= '|'.$server->__get('id').','.count($this->account['characters'][$server->__get('id')]);
				}
			}

			$this->send($packet);	
		}

		/**	@function	parseCommand
		 *	@abstract	Parses admin commands sent using the game admin console.
		 *	@param		string - Packet receved.
		 *	@return		void
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

		/**	@function	parseGameServer
		 *	@abstract	Parses game server redirection request.
		 *	@param		integer - server ID.
		 *	@return		void
		 */
		private function parseGameServer($id)
		{
			$serv = $this->game_servers->getServ($id);
			$token = Crypt\Random::generateKey(8);
			$this->send('AYK'.$serv->ip.':'.$serv->port.';'.$token); //TODO : Crypt IP
			//TODO : Sent "token" & "client informations" to the GameServer

		}

		/**	@function	parseBase
		 *	@abstract	Parses Base packets  (Packets sent after successful login).
		 *	@param		string - Packet receved.
		 *	@return		void
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

		/**	@function	disconnect
		 *	@abstract	Disconnects the client.
		 *	@return		void
		 */
		public function disconnect()
		{
			socket_close($this->socket);
			Logs::print_log('debug','Client disconnected');
			$this->kill();
		}
	}
?>