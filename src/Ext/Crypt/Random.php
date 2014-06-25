<?php

namespace Ext\Crypt;

class Random
{
	public static function generateKey($size = 8)
	{
		$chars = explode(" ",implode(" ",range('a','z'))." ".implode(" ",range('A','Z'))." ".implode(" ",range('0','9'))." - _");
    	$key = "";

    	for ($i=1; $i <= count($chars) ; $i++) 
        { 
    		$key .= $chars[rand(0,count($chars) - 1)];
    	}
    	return $key;
	}
}