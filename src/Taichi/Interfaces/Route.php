<?php
namespace Taichi\Interfaces;

interface Route
{
    public function get($route, $param);

    public function post($route, $param);

    public function put($route, $param);

    public function delete($route, $param);

    public function restful();
}