<?php

namespace Ext\Bilbon\ORM;

use OutOfBoundsException;

use PDO;

class Storage 
{
    public static $instances = array('default' => array());

    public static function get($name) 
    {
        if (!isset(self::$instances[$name])) 
            throw new OutOfBoundsException($name);
        return (($conf = self::$instances[$name]) instanceof PDO) ?
                self::$instances[$name] : self::$instances[$name] = new PDO(
                    empty($conf['dsn']) ? 'mysql:host=localhost' : $conf['dsn'],
                    empty($conf['user']) ? 'root' : $conf['user'],
                    empty($conf['password']) ? null : $conf['password'],
                    empty($conf['options']) ? array() : $conf['options']
                );
    }
}

?>