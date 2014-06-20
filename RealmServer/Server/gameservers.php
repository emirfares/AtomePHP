<?php
Class Server
{
	public $id = 1;
	public $ip = "127.0.0.1";
	public $state = 0;
	public $num_players = 0;
	public $port = "5555";
	public $system_port = "3333";
}
Class GameServers
{
	public $serverlist = array();


	function __construct()
	{
		$this->loadservers();
	}

	private function loadservers (){
		$db = DatabaseConnection::connect("realm");
		foreach($db->query("SELECT * FROM servers_list") as $data)
		{
			$serv = new Server;
			$serv->id = $data['id'];
			$serv->ip = $data['ip'];
			$serv->port = $data['port'];
			$serv->system_port = $data['system_port'];
			array_push($this->serverlist, $serv);
		}
	}

	public function ServerExists($id)
	{
		foreach($this->$serverlist as $serv)
		{
			if ($serv->id == $id)
			{
				return true;
			}
		}
		return false;
	}

	public function GetServ ($id)
	{
		foreach($this->$serverlist as $serv)
		{
			if ($serv->id == $id)
			{
				return $serv;
			}
		}
		
	}

	public function ChangeState ($servid,$state)
	{
		if ($this->ServerExists($servid))
		{
			$serv = $this->GetServ($servid);
			$serv->state = $state;
		}
	}

	public function ParsePacket ()
	{
		$packet ="";
		$first = true;
		foreach($this->serverlist as $serv)
		{
			if ($first)
			{
				$first = false;
			}
			else
			{
				$packet .= "|";
			}
			$packet .= $serv->id.";".$serv->state.";".(75 * $serv->id).";1";
		}
		return $packet;
	}
}

$GameServs = new GameServers;