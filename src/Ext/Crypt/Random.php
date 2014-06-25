<?php

namespace Ext\Crypt;

class Random
{
	public static function generateKey($size = 8)
	{
		$chars = explode(' ' ,implode(' ',range('a','z')).' '.implode(' ',range('A','Z')).' '.implode(' ',range('0','9')).' - _');
	    $count = mb_strlen($chars);

	    for ($i = 0, $str = ''; $i < $size; $i++) 
	    {
	        $index = rand(0, $count - 1);
	        $str .= mb_substr($chars, $index, 1);
	    }

	    return $str;
	}
}