<?php
namespace Bee\Core;

use ArrayAccess;
use ArrayIterator;

class Config extends ArrayIterator
{
    private static $instance = [];
    
    private $conf = [];
    
    public static function getInstance($key)
    {
        if (!isset(self::$instance[$key])) self::$instance[$key] = new self();
        
        return self::$instance[$key];
    }
    
    public function load($config_file)
    {
        $this->conf = Loader::load($config_file);

        return $this->conf;
    }
    
    public function get($key)
    {
        if (isset($this->conf[$key])) return $this->conf[$key];
        return '';
    }
    
    public function set($key, $value)
    {
        $this->conf[$key] = $value;
    }
    
    public function del($key)
    {
        if (isset($this->conf[$key])) {
            unset($this->conf[$key]); 
            return true;
        }
        
        return false;
    }
    
    public function all()
    {
        return $this->conf;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->conf[$offset]);
        //return parent::offsetExists($offset);
    }
    
    public function offsetGet($offset)
    {
        return $this->get($offset);
        //return parent::offsetGet($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
        //return parent::offsetSet($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        return $this->del($offset);
        //return parent::offsetUnset($offset);
    }
}