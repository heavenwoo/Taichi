<?php
namespace Bee\Core;

class Application
{
    private $controller_namespace = '';

    private static $instance = [];

    public static function init($app_name)
    {
        if (!isset(self::$instance[$app_name])) {
            self::$instance[$app_name] = new Application();
            
            $config = Config::getInstance('config')->load(TC_ROOT . 'Config' . DS . 'config.php');
            $config['app_name'] = $app_name;
            //Config::getInstance('config')->set('app_name', $app_name);

            self::$instance[$app_name]->controller_namespace = $config['app']['ns_prefix'] . $config['app']['controller_folder'] . '\\';
            
            Loader::addNamespace($config['app']['ns_prefix'], APP_PATH . $app_name . DS);
        }
        
        return self::$instance[$app_name];
    }

    public function run()
    {
        $controller = $model =isset($_GET) && isset($_GET['controller']) ? strtolower($_GET['controller']) . 'Controller' : 'mainController';
        $controller = $this->getControllerNamespace($controller);

        $action = isset($_GET['action']) ? ucfirst(strtolower($_GET['action'])) : 'Index';
        $controller = new $controller();
        $controller->$action();
    }

    private function getControllerNamespace($controller_name)
    {
        return $this->controller_namespace . $controller_name;
    }
}