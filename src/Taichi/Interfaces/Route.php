<?php
namespace Taichi\Interfaces;

interface Route
{
    public function get($route, $param);

    public function post();

    public function put();

    public function delete();

    public function restful();
}