<?php

namespace Ext\Crypt;

class Crypt
{
    public static function cryptPassword($pass, $key)
    {
        
        $chars = explode(' ' ,implode(' ',range('a','z')).' '.implode(' ',range('A','Z')).' '.implode(' ',range('0','9')).' - _');

        $l1 = $l2 = $l3 = $l4 = $l5 = 0; 
        $v1 = $v2 = null;
        $l7 = '1';
        for ($l1 = 0; $l1 <= strlen($pass) - 1; $l1++)
        {
            $l2 = ord(substr($pass, $l1, 1));
            $l3 = ord(substr($key, $l1, 1));
            $l5 = ($l2/16);
            $l4 = ($l2 % 16);
            $v1 = $chars[(($l5+$l3) % (count($chars))) % (count($chars))];
            $v2 = $chars[(($l4+$l3) % (count($chars))) % (count($chars))];
            $l7 = $l7.$v1.$v2;
        }
        
        return $l7."\n";
    }
}
?>