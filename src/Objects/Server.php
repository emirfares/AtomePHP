<?php

namespace Objects;

use Ext\Bilbon\ORM\ActiveRecord as ActiveRecord;

class Server extends ActiveRecord
{
	public static $table = 'gameservers';
	public static $storage = 'realm';

	public function __construct(array $server_informations, $em)
	{
		parent::__construct($server_informations, $em);
	}
}

?>