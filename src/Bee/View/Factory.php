<?php
namespace Bee\View;

class Factory
{
    private $template = null;
    
    public static function make(array $config, $app_name = '')
    {
        switch ($config['engine']) {
            case 'Smarty':
                //no break;
                
            default:
                break;
        }
    }
}