<?php
namespace Bee\View;

use Bee\Core\Config;

class View
{
    protected $template = null;
    
    public function __construct()
    {
        $config = Config::getInstance('config')->all();
        
        $this->template = TemplateFactory::make($config['view'], $config['app_name']);
    }
    
    public function display($tpl_name)
    {
        $this->template->display($tpl_name);
    }
    
    public function assign($var, $val = '')
    {
        $this->template->assign($var, $val);
    }
}