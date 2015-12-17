<?php
namespace Taichi\Http;

use Klein\Klein;
use Taichi\Interfaces\Route as RouteInterface;

class Router implements RouteInterface
{
    protected $routes = [];

    protected $controller = [];

    protected $action = [];

    protected $callbacks = [];

    protected $methods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ];

    protected $klein = null;

    public function __construct()
    {
        $this->klein = new Klein();
    }

    public function get($route, $param)
    {
        $this->register('GET', $route, $param);

        return $this;
    }

    public function post($route, $param)
    {
        $this->register('POST', $route, $param);

        return $this;
    }

    public function put($route, $param)
    {
        $this->register('PUT', $route, $param);

        return $this;
    }

    public function delete($route, $param)
    {
        $this->register('DELETE', $route, $param);

        return $this;
    }

    public function restful()
    {
        // TODO: Implement restful() method.
    }

    protected function register($method, $route, $param)
    {
        if (in_array($method, $this->methods)) {

        }
    }

    public function go()
    {

    }

    protected function parse($method, $args)
    {

    }
}