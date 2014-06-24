<?php

namespace Ext\Bilbon\ORM;

use PDO;

class ActiveRecord 
{
    protected $_data = array();
    protected $em;

    public static $table;
    public static $storage = 'default';
    public static $primary = 'id';
        
    public function __construct(array $data = null, $em)
    {
        if ($data)
            $this->_data = $data;
        if ($em)
            $this->em = $em;
    }
    
    public static function getTable() 
    {
        return (static::$table ? static::$table : strtolower(get_called_class()));
    }
    
    public function __get($property) 
    {
        return isset($this->_data[$property]) ? $this->_data[$property] : null;
    }
    
    public function __set($property, $value) 
    {
        $this->_data[$property] = $value;
    }

    public function __getAll()
    {
        return $this->_data;
    }
    
    public function update() 
    {
        $storage = $this->em == null ? Storage::get(static::$storage) : $this->em;
        $replace = array(
            '{table}' => static::getTable(),
            '{primary}' => static::$primary,
            '{sets}' => implode(',', array_map(function($property, $value) use($storage)
            {
                    return $property . '=' . $storage->quote($value);
            }, 
            array_keys($this->_data), array_values($this->_data))),
            '{value}' => $storage->quote($this->__get(static::$primary))
        );

        return $storage->exec(str_replace(array_keys($replace), array_values($replace), 'UPDATE {table} SET {sets} WHERE {primary} = {value}'));
    }
    
    public function delete() 
    {
        $storage = $this->em == null ? Storage::get(static::$storage) : $this->em;

        $replace = array(
            '{table}' => static::getTable(),
            '{primary}' => static::$primary,
            '{value}' => $storage->quote($this->__get(static::$primary))
        );
        
        return $storage->exec(str_replace(array_keys($replace), array_values($replace), 'DELETE FROM {table} WHERE {primary} = {value}')) > 0;
    }
    
    public function insert() 
    {
        $storage = $this->em == null ? Storage::get(static::$storage) : $this->em;

        $inserts = array(
            '{table}' => static::getTable(),
            '{properties}' => implode(',', array_keys($this->_data)),
            '{values}' => implode(',', array_map(array(Storage::get(static::$storage), 'quote'), array_values($this->_data)))
        );
        
        $this->__set(
            static::$primary,
            $storage->exec(str_replace(array_keys($insters), array_values($inserts), 'INSERT INTO {table} ({properties}) VALUES ({values})')) > 0 ?
                $storage->lastInsertId() : null
        );
    }
        
    public static function request($sql, array $params = null) 
    {
        $storage = $this->em == null ? Storage::get(static::$storage) : $this->em;

        $replace = $params ? array_map(array($storage, 'quote'), $params) : array();
        $replace['{table}'] = static::getTable();

        return ($result = $storage->query(
            str_replace(array_keys($replace), array_values($replace), $sql), PDO::FETCH_CLASS, get_called_class())) 
            ? $result->fetchAll() : array();
    }
    
    public function __toString() 
    {
        return static::$storage.'.'.static::$table.'@'.get_class($this)." {\n".implode("\n", array_map(function($property, $value) 
        {
            return ' '.str_pad($property, 8).': '.(strlen($value) > 60 ? substr($value, 0, 60).'...' : $value);
        }, array_keys($this->_data), array_values($this->_data)))."\n}\n";
    }
}