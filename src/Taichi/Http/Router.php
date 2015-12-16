<?php
namespace Taichi\Http;

class Router
{
    protected $routes = [];

    protected $methods = [
        'get',
        'post',
        'put',
        'delete',
    ];

    public static function get()
    {

    }

    public function go()
    {

    }

    protected function parse($method, $args)
    {

    }

    public function __callStatic($method, $args)
    {
        if (in_array($method = strtolower($method), $this->methods)) {
            return $this->parse($method, $args);
        }

        throw new \InvalidArgumentException('Not defined router');
    }
}