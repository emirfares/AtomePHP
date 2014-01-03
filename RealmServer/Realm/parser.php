<?php
Class Parser Extends Thread
{
	//public $Client;
	public function __construct($tclient,$packet){
		global $Config;
		$this->Client = $tclient;
		$this->Packet = $packet;
		$this->Config = $Config;

	}
	public function run()	{

		global $Config;
		Parser::ParsePacket($this->Client,$this->packet);
	}
	public function ParsePacket ($tClient,$packet){
		global $Config;
		switch ($this->Client->state)  {
			case "version":
				if ($this->Packet == $this->Config->client_version) {
					$this->Client->state = "account";
					print "client connected\n";
				}
				else{
					send($this->Client,"AlEv".$this->Config->client_version);
					print "Wrong Version ({$packet})from client :".$this->Client->getip()."\n";
					$this->Client->state = "none";
				}
			break;

			case "account":
				Parser::ParseAccount($this->Client,$this->Packet);

			break;

			case "base":
			Parser::ParseBase ($this->Client,$this->Packet);
			break;

		}
	}

	protected function ParseAccount ($tClient,$packet){
		global $GameServs;
				$pack = explode("#",$packet);

				$username = strtolower($pack[0]);

				$cryptedpass = trim($pack[1]);
				$infos = New RealmClient($username);
				
				if ($infos->account_exists){
					$tpass = trim(CryptPass($infos->password,$tClient->key));
					if ($username == strtolower($infos->username) && $tpass == $cryptedpass) {
						if(!$infos->banned)
						{
							$tClient->state = "base";
							$tClient->Infos = $infos;
							send($tClient,"Ad".$infos->account_name);
							send($tClient,"Ac0");
							send($tClient,"AH".$GameServs->ParsePacket());
							send($tClient,($infos->gmlevel>0) ? "Alk1" : "Alk0");
							send($tClient,"AQ");							
							print_info("Connected to the login server\n");
						}
						else
						{
							$tClient->State = "none";
							send($tClient,"AlEb");
							print_warning("Client \"".$username."\" Banned !");
						}
						

					}
					else{
						$tClient->State = "none";
						send($tClient,"AlEf");
						
						
					}
				}
				else{
					$tClient->State = "none";	
					send($tClient,"AlEf");
					$tClient->State = "none";
				}
				
	}

	protected function SendCharactersList ($tClient)
	{
		global $Config,$GameServs;
		$time = "";
		if ($Config->subscription)
		{
			$time = 0;
		}
		else
		{
			$time = (365 * 24 * 3600) * 1000;
		}
		$packet = "AxK".$time;

		foreach ($GameServs->serverlist as $serv)
		{
			if (array_key_exists($serv->id, $tClient->Infos->characters))
			{
				$packet .= "|".$serv->id.",".count($tClient->Infos->characters[$serv->id]);
			}
		}

		send($tClient,$packet);	
	}
	protected function ParseCommand ($tClient,$packet)
	{
		$p = explode(" ",$packet);
		switch (strtolower($p[0])) {
			case 'packet':
				send($tClient,$p[1]);
				break;
			
			default:
				send($tClient,"BAE");
				break;
		}
	}
	protected function ParseGameServer ($id)
	{

	}
	protected function ParseBase ($tClient,$packet){

		$p = substr($packet, 0,2);
		switch ($p)
		{
			case "Ax":
				Parser::SendCharactersList($tClient);
			break;

			case "AX":
				Parser::ParseGameServer(substr($packet, 2));
			break;

			case "Af":
			break;

			case "BA":
			Parser::ParseCommand($tClient,substr($packet, 2));
			break;
		}
	}

}
