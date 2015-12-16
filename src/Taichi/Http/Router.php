<?php
namespace Taichi\Http;

use Taichi\Interfaces\Route as RouteInterface;

class Router implements RouteInterface
{
    protected $routes = [];

    protected $controller = null;

    protected $action = null;

    protected $methods = [
        'get',
        'post',
        'put',
        'delete',
    ];

    public function get($route, $param)
    {
        // TODO: Implement get() method.
    }

    public function post()
    {
        // TODO: Implement post() method.
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function restful()
    {
        // TODO: Implement restful() method.
    }

    public function go()
    {

    }

    protected function parse($method, $args)
    {

    }
}