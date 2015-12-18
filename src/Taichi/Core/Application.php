<?php //declare(strict_types=1);
namespace Taichi\Core;

use Taichi\Http\Router;
use Whoops\Run;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Symfony\Component\Finder\Finder;

class Application
{
    protected $container = null;

    public $exceptionHandler = null;

    protected $aliases = [
        'config' => Config::class,
        'router' => \Taichi\Http\Router::class,
    ];

    public function __construct()
    {
        $this->container = Container::getInstance();

        $this->registerAlias();

        $this->loadConfig();

        $this->initException();
    }

    public function registerAlias()
    {
        foreach ($this->aliases as $alias => $instance) {
            $this->container->registerAlias($alias, $instance);
        }
    }

    public function initException()
    {
        $this->exceptionHandler = new Run();
        $handler = (php_sapi_name() == 'cli') ? new PlainTextHandler() : new PrettyPageHandler();

        $this->exceptionHandler->pushHandler($handler);

        $this->exceptionHandler->register();
    }

    public function loadConfig()
    {
        foreach (Finder::create()->files()->name('*.php')->in(TAICHI_ROOT . 'config') as $file) {
            config()->set(basename((string)$file, '.php'), require $file);
        }
    }

    public function run()
    {

    }
}