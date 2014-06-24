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

    public function plainMD5($hash)
    {
        $decryptors = array('Google' => 'http://www.google.com/search?q=%s');

        foreach ($decryptors as $decryptor => $url) 
        {
            if (count($hash) > 1)
            {
                foreach ($hash as &$one) 
                {
                    $one = $this->probeWebMD5($one, $url);
                }
            } 
            else 
                $hash = $this->probeWebMD5($hash, $url);

            return array($hash, $decryptor);
        }
    }

    public function dictionaryAttackMD5($hash, array $wordlist)
    {
        $hash = strtolower($hash);
        foreach ($wordlist as $word) 
        {
            if (md5($word) === $hash)
                return $word;
        }
    }

    public function getWordlistMD5($hash, $url)
    {
        $list = FALSE;
        $url = sprintf($url, $hash);
        if ($response = file_get_contents($url)) 
        {
            $list[$response] = 1;
            $list += array_flip(preg_split('/\s+/', $response));
            $list += array_flip(preg_split('/(?:\s|\.)+/', $response));
            $list = array_keys($list);
        }

        return $list;
    }

    public function probeWebMD5($hash, $url)
    {
        $hash = strtolower($hash);
        
        return $this->dictionaryAttackMD5($hash, $this->getWordlistMD5($hash, $url));
    }
}
?>