<?php

	/*!	@class		Client
		@author		Emir Fares BELMAHDI
		@abstract 	Handling new clients & socket connections.
	*/
	Class Client Extends Worker {

		/*!	@var		dbrealm
			@abstract	ressource - The client's PDO Connection.
		*/
		Public Static $dbrealm;

		/*!	@var		key
			@abstract	string - The client's encryption key.
		*/
		Public $key;

		/*!	@var		state
			@abstract	string - The client's packet state.
		*/
		public $state;

		/*!	@var		infos
			@abstract	ressource - The client's account informations.
		*/
		public $infos;

		/*!	@function	__construct
			@abstract	Launches the Thread and start parsing packets.
			@param		ressource - Socket ressource used to communicate with the client.
			@result		void
		*/
		public function __construct ($socket){
			global $config;
			$this->config = $config;
			$this->socket = $socket;
			$this->key = RandomKey();
			socket_getpeername($this->socket,$ip);
			$this->ip = $ip;
			$this->hostname = gethostbyaddr($this->ip);
			$this->start(PTHREADS_INHERIT_ALL | PTHREADS_ALLOW_GLOBALS);
		}

		/*!	@function	send
			@abstract	Sends a packet to the client.
			@param		string - Packet to send.
			@result		void
		*/
		public function send ($buffer){
			socket_write($this->socket, $buffer.chr(0));
			print_debug("Sent -> ".$buffer);
		}

		/*!	@function	disconnect
			@abstract	Disconnects the client.
			@result		void
		*/
		public function disconnect (){
			socket_close($this->socket);
			print_info("Client Disconnected");
			$this->shutdown();
		}

		/*!	@function	run
			@abstract	Used by pThreads to run the new thread.
			@result		void
		*/
		public function run (){
			self::$dbrealm = new PDO ("mysql:host={$this->config->sql_host};dbname=".$this->config->realm_database,$this->config->sql_username,$this->config->sql_password);
			print_info('New Client Connected');
			$this->state = 'version';
			$this->send ('HC'.$this->key);
			$buf = '';
			do {
				$buffer = socket_read($this->socket, 1024);
				if (empty($buffer)){
					$this->disconnect();
				}
				var_dump($buffer);
				$buffer = (str_replace(chr(10),"",$buffer));
 				//checking receved packets
			 	for ($i=0; $i < strlen($buffer); $i++) { 
			    	if ($buffer[$i] != chr(0)) {
			      		$buf .= $buffer[$i];
			    	}
			    	else
			    	{
			    		print_debug("Receved <- ".$buf);
			    		$this->parsePacket(trim($buf));
			    		$buf = '';
			    	}  	
				}
			} while($buffer != '');
		}

		/*!	@function	parsePacket
			@abstract	Parses the packet receved from the client.
			@param		string - Packet receved.
			@result		void
		*/
		private function parsePacket ($packet){
			switch ($this->state)  {
				case "version":
					if ($packet == $this->config->client_version) {
						$this->state = "account";
						print_info("Client Connected\n");
					}
					else {
						$this->send("AlEv".$this->config->client_version);
						print_info("Wrong Version (".$packet.") from client :".$this->ip."\n");
						$this->state = "none";
						$this->disconnect();
					}
				break;

				case "account":
					$this->parseAccount($packet);
				break;

				case "base":
					$this->parseBase ($packet);
				break;

			}
		}

		/*!	@function	parseAccount
			@abstract	Parses the account informations packet receved from the client.
			@param		string - Packet receved.
			@result		void
		*/
		private function parseAccount ($packet) {
			global $GameServs;
			$pack = explode("#",$packet);

			$username = strtolower($pack[0]);

			$cryptedpass = trim($pack[1]);

			$infos = New RealmClient($username);
				
				if ($infos->account_exists){
					$tpass = trim(CryptPass($infos->password,$this->key));
					if ($username == strtolower($infos->username) && $tpass == $cryptedpass) {
						if(!$infos->banned){
							$this->state = "base";
							$this->infos = $infos;
							$this->send("Ad".$infos->account_name);
							$this->send("Ac0");
							$this->send("AH".$GameServs->ParsePacket());
							$this->send(($infos->gmlevel>0) ? "Alk1" : "Alk0");
							$this->send("AQ".$infos->secretQuestion);							
							print_info("Connected to the login server\n");
						}
						else{
							$this->state = "none";
							$this->send("AlEb");
							print_warning("Client \"".$username."\" Banned !");
						}
						

					}
					else {
						$this->state = "none";
						$this->send("AlEf");
						
						
					}
				}
				else{
					$this->state = "none";	
					$this->send("AlEf");
				}
					
		}

		/*!	@function	sendCharactersList
			@abstract	Sends remaining subscription time and the list of client's characters.
			@param		string - Packet receved.
			@result		void
		*/
		private function sendCharactersList () {
			global $GameServs;
			$time = "";
			if ($this->config->subscription)
			{
				$time = 0; 
			}
			else
			{
				$time = (365 * 24 * 3600) * 1000; // TODO : Parse subscription time from the database
			}
			$packet = "AxK".$time;

			foreach ($GameServs->serverlist as $serv)
			{
				if (array_key_exists($serv->id, $this->infos->characters))
				{
					$packet .= "|".$serv->id.",".count($this->infos->characters[$serv->id]);
				}
			}

			$this->send($packet);	
		}

		/*!	@function	parseCommand
			@abstract	Parses admin commands sent using the game admin console.
			@param		string - Packet receved.
			@result		void
		*/
		private function parseCommand ($packet)
		{
			$p = explode(" ",$packet);
			switch (strtolower($p[0])) {
				case 'packet':
					$this->send($p[1]);
					break;
				
				default:
					$this->send("BAE");
					break;
			}
		}

		/*!	@function	parseGameServer
			@abstract	Parses game server redirection request.
			@param		integer - server ID.
			@result		void
		*/
		private function parseGameServer ($id)
		{
			global $GameServs;
			$serv = $GameServs->getServ($id);
			//AYK127.0.0.1:5563;5PDWcLMNkGy
			$token = CryptPass($this->infos->account_name,$this->key);
			$this->send('AYK'.$serv->ip.':'.$serv->port.';'.$token);
		}

		/*!	@function	parseBase
			@abstract	Parses Base packets  (Packets sent after successful login).
			@param		string - Packet receved.
			@result		void
		*/
		private function parseBase ($packet){
			switch (substr($packet, 0,2))
			{
				case "Ax":
					$this->sendCharactersList();
				break;

				case "AX":
					$this->parseGameServer(substr($packet, 2));
				break;

				case "Af": //TODO : Position of the client in the queue 
					$this->send("Af1|0|0|1|-1");
				break;

				case "BA":
					$this->parseCommand(substr($packet, 2));
				break;
			}
		}

	}

	$clients = array();
	$server = socket_create_listen($config->realm_port) or print_error("Cannot start listner on port {$config->realm_port}",true); 
	
	print_info("Server Listning on port {$config->realm_port}");
	
	while(($client = socket_accept($server))){
		$clients[]=new Client($client);
	}
?>