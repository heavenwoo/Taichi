<?php
namespace Taichi\Database\Connection;

class PgSqlConnection extends PdoConnection
{
    public function __construct($dsn, $user, $passwd, $options = [])
    {
        $this->connect($dsn, $user, $passwd, $options = []);
    }
}