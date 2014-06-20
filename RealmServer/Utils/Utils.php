<?php
    $chars = explode(" ",implode(" ",range('a','z'))." ".implode(" ",range('A','Z'))." ".implode(" ",range('0','9'))." - _");
    function RandomKey (){
    	global $chars;
    	$key = "";

    	for ($i=1; $i <= count($chars) ; $i++) { 
    		$key .= $chars[rand(0,count($chars) - 1)];
    	}
    	return $key;
    }
    function CryptPass($pass,$key){
        global $chars;
        $l1 = $l2= $l3= $l4= $l5=0 ; 
        $v1 = $v2 = "";
        $l7 = "1";
        for ($l1 = 0; $l1<= strlen($pass)-1; $l1++){
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
?>