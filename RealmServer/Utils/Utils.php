<?php

$chars = explode(" ",implode(" ",range('a','z'))." ".implode(" ",range('A','Z'))." ".implode(" ",range('0','9'))." - _");
function RandomKey ($Lenght){
	Global $chars;
	$key = "";

	for ($i=1; $i <= count($chars) ; $i++) { 
		$key .= $chars[rand(0,count($chars) - 1)];
	}
	return $key;
}
function CryptPass($pass,$key)
    {
         //$hash = array ("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "-", "_");
         global $chars;
         $l1 = $l2= $l3= $l4= $l5=0 ; 
         $v1 = $v2 = "";
         $l7 = "1";
         for ($l1 = 0; $l1<= strlen($pass)-1; $l1++)
           {
            $l2 = ord(substr($pass,$l1,1));
            $l3 = ord(substr($key,$l1,1));
            $l5 = ($l2/16);
            $l4 = ($l2 % 16);
            $v1 = $chars[(($l5+$l3) % (count($chars))) % (count($chars))];
            $v2 = $chars[(($l4+$l3) % (count($chars))) % (count($chars))];
            $l7 = $l7.$v1.$v2;
           }
          return $l7."\n";
    }

//print $char[1];