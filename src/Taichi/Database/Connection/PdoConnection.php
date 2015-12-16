<?php
namespace Taichi\Database\Connection;

use PDO;
use Exception;
use Taichi\Exception\CoreException;

abstract class PdoConnection
{
    protected $pdo = null;
    
    public function connect($dsn, $user, $passwd, $options = [])
    {
        try {
            $this->pdo = new PDO($dsn, $user, $passwd, $options);
        } catch (Exception $e) {
            throw new CoreException($e->getMessage());
        }
    }
    
    public function getPdo()
    {
        return $this->pdo;
    }

    public function select($query, $bindings)
    {
        foreach ($bindings as $key => $value) {
            if ($value == false) {
                $bindings[$key] = 0;
            }
        }
        dump($bindings);

        $result = $this->getPdo()->prepare($query)->execute($bindings);

        dump($result);

        return $result;
    }
}