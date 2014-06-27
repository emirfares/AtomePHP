<?php

namespace Ext\Crypt;

class Random
{
	public static function generateKey($size = 64)
	{
		$chars = explode(" ",implode(" ",range('a','z'))." ".implode(" ",range('A','Z'))." ".implode(" ",range('0','9'))." - _");
    	$key = "";
        if($size > ($s = count($chars)))
            $size = $s;

    	for ($i=1; $i <= $size ; $i++) 
        { 
    		$key .= $chars[rand(0,count($chars) - 1)];
    	}
    	return $key;
	}
}