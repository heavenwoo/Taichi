<?php
namespace Bee\Database;

use PDO;
use Bee\Database\Query\Builder;
use Bee\Database\Query\Grammar\Grammar;
use Bee\Exception\CoreException;
use Bee\Database\Connection\MySqlConnection;
use Bee\Database\Connection\PgSqlConnection;
use Bee\Database\Connection\SQLiteConnection;

class Factory
{
    public static function make($dsn)
    {
        $dsn = self::parse($dsn);

        switch (strtolower($dsn['dbtype'])) {
            case 'mysql' :
            case 'mysql.sock':
                return new Builder(new MySqlConnection(self::setDsn($dsn), $dsn['user'], $dsn['pass'], $dsn['options']), new Grammar(), $dsn['tb_prefix']);
                break;
        
            case 'sqlite':
                return new Builder(new SQLiteConnection(self::setDsn($dsn), $dsn['user'], $dsn['pass'], $dsn['options']), new Grammar(), $dsn['tb_prefix']);
                break;
        
            case 'pgsql':
                return new Builder(new PgSqlConnection(self::setDsn($dsn), $dsn['user'], $dsn['pass'], $dsn['options']), new Grammar(), $dsn['tb_prefix']);
                break;
                
            default:
                throw new CoreException('Unknown Database');
        }
    }
    
    private static function parse($url)
    {
        if (strpos($url, "://")) {
            $dsn_array = @parse_url($url);
            if (!empty($dsn_array["scheme"])) {
                if (isset($dsn_array["query"])) {
                    $option_array = explode('&', $dsn_array['query']);
                    foreach($option_array as $element => $value) {
                        $array = explode('=', $value);
                        $data = isset($array[1]) ? $array[1] : 1;
                        switch(strtolower($array[0])) {
                            case "charset":
                                $dsn_array["charset"] = $data;
                                break;
                            case "persist":
                            case "persistent":
                                $dsn_array['options'][PDO::ATTR_PERSISTENT] = true;
                                break;
                            case "fetchmode":
                                switch (strtolower($data)) {
                                    case "assoc":
                                        $dsn_array['options'][PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
                                        break;
                                    case "both":
                                        $dsn_array['options'][PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_BOTH;
                                        break;
                                }
                        }
                    }
                }
                //$options[PDO::ATTR_DEFAULT_FETCH_MODE] = isset($options[PDO::ATTR_DEFAULT_FETCH_MODE]) ? $options[PDO::ATTR_DEFAULT_FETCH_MODE] : PDO::FETCH_ASSOC;
            } else {
                throw new CoreException('No database type identified!');
            }
        } else {
            throw new CoreException('DSN parse failed!');
        }

        $dsn = array();

        $dsn['dbtype'] = $dsn_array['scheme'];
        $dsn['host'] = $dsn_array['host'];
        $dsn['user'] = isset($dsn_array["user"]) ? $dsn_array["user"] : '';
        $dsn['pass'] = isset($dsn_array["pass"]) ? $dsn_array["pass"] : '';
        $dsn['port'] = isset($dsn_array["port"]) ? $dsn_array["port"] : '';
        $dsn['charset'] = isset($dsn_array["charset"]) ? $dsn_array["charset"] : '';
        $dsn['options'] = isset($dsn_array['options']) ? $dsn_array['options'] : '';
        $dsn['database'] = isset($dsn_array['path']) ? substr($dsn_array['path'], 1) : '';
        $dsn['tb_prefix'] = isset($dsn_array['fragment']) ? $dsn_array['fragment'] : '';
        
        if ($dsn_array['scheme'] == 'mysql.sock') {
            //$dsn['dbtype'] = 'mysql';
            $dsn['database'] = $dsn_array['host'];
            $dsn['host'] = $dsn_array['path'];
        }
        unset($url, $dsn_array);
        
        return $dsn;
    }
    
    private static function setDsn($dsn)
    {
        switch (strtolower($dsn['dbtype'])) {
            case 'mysql':
                $dsn_string = 'mysql:host=' . $dsn['host'] . '; dbname=' . $dsn['database'];
                if ($dsn["port"] != '') $dsn_string .= "; port=" . $dsn['port'];
                if ($dsn["charset"] != '') $dsn_string .= "; charset=" . $dsn['charset'];
                break;

            case 'mysql.sock':
                $dsn_string = 'mysql:unix_socket=' . $dsn['host'] . '; dbname=' . $dsn['database'];
                if ($dsn["port"] != '') $dsn_string .= "; port=" . $dsn['port'];
                if ($dsn["charset"] != '') $dsn_string .= "; charset=" . $dsn['charset'];
                break;
                
            case 'sqlite':
                $dsn_string = 'sqlite:';
                $dsn_string .= ($dsn['database'] != '') ? $dsn['database'] : $dsn['host'];
                break;
                
            case 'pgsql':
                $dsn_string = 'pgsql:host=' . $dsn['host'] . '; dbname=' . $dsn['database'];
                break;
        }

        return $dsn_string;
    }
}