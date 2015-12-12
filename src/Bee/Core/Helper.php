<?php
namespace Bee\Core;

class Helper
{
    public static function getRandom($len = 8, $type = 1, $key = 1)
    {
        $str = $chars = '';
        switch($type)
        {
            case '1':
                $chars = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789';
                break;
            case '2':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case '3':
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case '4':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case '5':
                $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case '6':
                $chars = '0123456789';
                break;
        }
    
        $length = strlen( $chars ) - 1;
    
        srand((double)microtime() * $key * 1000000);
    
        for ( $i = 0; $i < $len; $i++ ) {
            $str .= substr($chars, rand(0, $length), 1);
        }

        return $str;
    }
}