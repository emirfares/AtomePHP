<?php
Class RealmClient
{
	public $id = -1;
	public $account_exists = false;
	public $username,$password,$account_name,$secretQuestion;
	public $banned = false;

	public $gmlevel,$points,$originalpoints;
	public $gifts,$subscriptiondate,$lastconnection,$lastip;

	public $characters = array();

	function __construct ($account)
	{
		$this->loadclient($account);
	}

	protected function loadclient ($account){
		$sql = Client::$dbrealm->prepare("SELECT * FROM player_accounts WHERE username=?");
		$sql->execute(array($account)); 
		$count = $sql->RowCount();
		if ($count == 1) {
			$this->account_exists = true;
			$data = $sql->fetch(PDO::FETCH_ASSOC);
			$this->id = $data['id'];
			$this->username = $data['username'];
			$this->password = $data['password'];
			$this->account_name = $data['pseudo'];
			$this->gmlevel = $data['gmlevel'];
			$this->points = $data['points'];
			$this->secretQuestion = $data['question'];
			$this->banned = ($data['banned']>0) ? true : false;
			$this->lastip = $data['lastIP'];
			$this->lastconnection = $data['last_time'];
			($data['characters'] != "") ? $this->characters = $this->parseCharacter($data['characters']): $this->characters = array();
		}
		else
		{
			$this->account_exists = false;
		}

	}

	private function parseCharacter ($packet){
		$tdata = explode("|",$packet);
		$char = array();
		foreach ($tdata as $data)
		{
			if (strpos($data, ',') !== false)
			{
				$d = explode(",", $data);
				if (!array_key_exists($d[1], $char))
				{
					$char[$d[1]] = array();			
				}
				array_push($char[$d[1]],$d[0]);
			}
		}
		return $char;
	}
}
