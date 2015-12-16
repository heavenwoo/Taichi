<?php
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
$html = <<<TC
<!DOCTYPE html>
<html>

 <head>
        <title>
            some_resource
        </title>
    </head>
    <body>
  <div class="content">
  <form method="HEAD" action="/delete" enctype="application/x-www-form-urlencoded">
   <div>
    <input id="title" name="title" placeholder="The Resource Title" type="text" value="The Resource Title" autofocus />
   </div>
   <div>
    <textarea id="body" name="body" placeholder="The Resource Body" required>The Resource Body</textarea>
   </div>
   <div>
    <input id="submit" name="submit" type="submit" value="Submit" />
   </div>
   <!-- Resource Settings -->
   <ul>
    <li>
     <input id="listed" name="listed" type="checkbox" value="y" />
    </li>
    <li>
     <input id="template" name="template" type="text" value="resource.html" />
    </li>
   </ul>
  </form>
  </div>
    </body>
</html>
TC;
//echo $html;
        //dd(file_get_contents('php://input', 'r'), $_SERVER, $_REQUEST, $_REQUEST['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? 'none');
        router('get', '/', '@todo');
        router()->get('/post', 'PostController#index');
    }

    public function registerAlias()
    {
        foreach ($this->aliases as $alias => $instance) {
            $this->container->registerAlias($alias, $instance);
        }
    }

    public function initException()
    {
        $this->exceptionHandler     = new Run();
        $handler = (php_sapi_name() == 'cli') ? new PlainTextHandler() : new PrettyPageHandler();

        $this->exceptionHandler->pushHandler($handler);

        $this->exceptionHandler->register();
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