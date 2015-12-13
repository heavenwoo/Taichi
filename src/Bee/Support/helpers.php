<?php
use Bee\Core\Container;
use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('ioc')) {
    function ioc($build = null, $parameters = [])
    {
        if (is_null($build)) {
            return $c = Container::getInstance();
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
            return ioc(Bee\Core\Config::class);
        }
        if (is_array($key)) {
            return ioc(Bee\Core\Config::class)->set($key);
        }

        return ioc(Bee\Core\Config::class)->get($key, $default);
    }
}