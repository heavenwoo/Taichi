<?php
namespace Bee\Core;

use ArrayAccess;

class Config implements ArrayAccess
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get($key, $default = null)
    {
        return (isset($this->items[$key])) ? $this->items[$key] : $default;
        //return $this->items[$key] ?? $default; //PHP 7.0
    }
    
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }
    
    public function del($key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return true;
        }
        
        return false;
    }
    
    public function all()
    {
        return $this->items;
    }
    
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    public function offsetUnset($offset)
    {
        return $this->del($offset);
    }
}