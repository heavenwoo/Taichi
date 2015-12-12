<?php
namespace Bee\Model;

use Bee\Core\Config;
use Bee\Database\Factory;

class Model
{
    public function __construct()
    {

    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'link':
                return Factory::make(Config::getInstance('config')->get('dsn'));
                break;
        }
    }
}