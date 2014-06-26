<?php

	namespace Objects;

	use Ext\Bilbon\ORM\ActiveRecord as ActiveRecord;

	class Account extends ActiveRecord
	{
		public static $table = 'player_accounts';
		public static $storage = 'realm';

		public function __construct(array $account_informations, $em)
		{
			$account_informations['characters'] = $account_informations['characters'] != null ? $this->parseCharacters($account_informations['characters']) : array();
			$account_informations['is_banned'] = $account_informations['is_banned'] > 0 ? true : false;

			parent::__construct($account_informations, $em);
		}

		private function parseCharacters($characters)
		{
			$tdata = explode("|",$characters);
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

?>