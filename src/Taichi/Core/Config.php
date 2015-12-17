<?php
namespace Taichi\Core;

use ArrayAccess;
use Taichi\Interfaces\Config as ConfigInterface;

class Config implements ArrayAccess, ConfigInterface
{
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function has($key)
    {
        return isset($this->items[$key]);
    }

    public function get($key, $default = null)
    {
        if (is_null($key)) {
            return $this->items;
        }

        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        $value = $this->items;
        foreach (explode('.', $key) as $seg) {
            if (!array_key_exists($seg, $value)) {
                return $default;
            }
            $value = $value[$seg];
        }

        return $value;
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