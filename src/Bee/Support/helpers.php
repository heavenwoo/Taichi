<?php
use Bee\Core\Container;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('app')) {
    function app($build = null, $parameters = [])
    {
        if (is_null($build)) {
            return Container::getInstance();
        }

        return Container::getInstance()->build($build, $parameters);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($d) {
            VarDumper::dump($d);
        }, func_get_args());

        die(1);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app(Bee\Core\Config::class);
        }
        if (is_array($key)) {
            return app(Bee\Core\Config::class)->set($key);
        }

        $abstract = abstract2array($key);
        $key = end($abstract);
        array_pop($abstract);

        $items = require BEE_ROOT . 'config' . DS . implode($abstract, DS) . '.php';

        return app(Bee\Core\Config::class, $items)->get($key, $default);
    }
}

if (!function_exists('abstract2array')) {
    function abstract2array($abstract)
    {
        if (strrpos($abstract, '.') == 0)
        {
            return $abstract;
        }

        return explode('.', $abstract);
    }
}