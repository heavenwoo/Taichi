<?php
use Taichi\Core\Container;
use Taichi\Support\Dumper;

if (!function_exists('ioc')) {
    function ioc($build = null, $parameters = [])
    {
        if (is_null($build)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($build, $parameters);
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
            Dumper::dump($d);
        }, func_get_args());

        die(1);
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return ioc('config');
        }
        if (is_array($key)) {
            return ioc('config')->set($key);
        }

        return ioc('config')->get($key, $default);
    }
}