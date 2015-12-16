<?php
namespace Taichi\Core;

use Symfony\Component\Finder\Finder;

class Application
{
    protected $container = null;

    protected $aliases = [
        'config' => Config::class,
    ];

    public function __construct()
    {
        $this->container = Container::getInstance();

        $this->registerAlias();

        $this->loadConfig();
    }

    public function registerAlias()
    {
        foreach ($this->aliases as $alias => $instance) {
            $this->container->registerAlias($alias, $instance);
        }
    }

    public function loadConfig()
    {
        foreach(Finder::create()->files()->name('*.php')->in(TAICHI_ROOT . 'config') as $file) {
            config()->set(basename($file, '.php'), require $file);
        }
    }

    public function run()
    {

    }
}