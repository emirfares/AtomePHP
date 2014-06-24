<?php

namespace Ext\Crypt;

class Random
{
	public static function generateStr($size = 8)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	    $count = mb_strlen($chars);

	    for ($i = 0, $str = ''; $i < $size; $i++) 
	    {
	        $index = rand(0, $count - 1);
	        $str .= mb_substr($chars, $index, 1);
	    }

	    return $str;
	}
}