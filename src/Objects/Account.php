<?php

namespace Objects;

use Ext\Bilbon\ORM\ActiveRecord as ActiveRecord;

class Account extends ActiveRecord
{
	public static $table = 'player_accounts';
	public static $storage = 'realm';

	public function __construct(array $account_infirmations, $em)
	{
		$account_infirmations['characters'] = $account_infirmations['characters'] != null ? $this->parseCharacters($account_infirmations['characters']) : array();
		$account_infirmations['is_banned'] = $account_infirmations['is_banned'] > 0 ? true : false;

		parent::__construct($account_infirmations, $em);
	}

	private function parseCharacters($characters)
	{
		$array_characters = array();

		if (strpos($characters, ',') !== false)
		{
			$d = explode(',', $characters);

			if (!array_key_exists($d[1], $array_characters))
			{
				$array_characters[$d[1]] = array();			
			}

			array_push($array_characters[$d[1]], $d[0]);
		}

		return $array_characters;
	}
}

?>