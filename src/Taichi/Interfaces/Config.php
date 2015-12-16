<?php
namespace Taichi\Interfaces;

interface Config
{
    public function get($key, $default = null);

    public function set($key, $value);

    public function has($key);

    public function del($key);

    public function all();
}