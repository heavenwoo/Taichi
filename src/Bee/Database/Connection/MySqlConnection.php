<?php
namespace Bee\Database\Connection;

class MySqlConnection extends PdoConnection
{
    public function __construct($dsn, $user, $passwd, $options = [])
    {
        $this->connect($dsn, $user, $passwd, $options = []);
    }
}