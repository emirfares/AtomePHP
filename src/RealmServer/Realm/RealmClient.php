<?php

namespace RealmServer\Realm;

use Objects\Account as Account;
use RealmServer\Main\Client as Client;

use PDO;

class RealmClient
{	
	public $account_exists = false;
	public $account;

	function __construct($account)
	{
		$this->loadClient($account);
	}

	private function loadClient($account)
	{
		$client = Client::$db_realm->prepare('SELECT * FROM player_accounts WHERE username = ?');
		$client->execute(array($account));

		if ($client->rowCount())
		{
			$this->account_exists = true;
			$this->account = new Account($client->fetch(PDO::FETCH_ASSOC), Client::$db_realm);
		}
		else
			$this->account = false;
	}
}

?>