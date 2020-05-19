<?php
namespace Manager\Container;

use Database\Database;
use Reflection\Reflector;
use Database\Eloquent;
use Request\Request;
use Routing\Router;
use ReflectionParameter;

class Application extends Reflector
{
    private $aliases = [];

    private $isBooted = false;

    private static $instance;

    private function __construct()
    {
    }

    public function start()
    {
        if(!$this->isBooted)
            $this->registerCore();

        $this->loadCore();
    }

    public static function getInstance()
    {
        if(is_null(self::$instance))
            self::$instance = new Application();

        return self::$instance;
    }

    public function register($alias, $class)
    {
        $alias = ucfirst($alias);

        if(!$this->has($alias)){
            $this->add($alias, $this->resolve($class));
        }
    }

    private function registerCore()
    {
        $cores = ['Database' => Database::class, 'Router' => Router::class, 'Request' => Request::class, 'Eloquent' => Eloquent::class, 'Reflector' => Reflector::class];

        foreach ($cores as $alias => $core)
        {
            $this->register($alias, $core);
        }

        $this->isBooted = true;
    }

    /**
     * @param string $alias
     * @return object
     */
    public function get($alias)
    {
        $alias = ucfirst($alias);

        if($this->has($alias))
        {
            return $this->aliases[$alias];
        }

        return new \Exception("$alias is not registered");
    }

    private function add($alias, $class)
    {
        $this->aliases[$alias] = $class;
    }

    public function has($alias)
    {
        $alias = ucfirst($alias);

        if($alias instanceof ReflectionParameter)
        {
            return isset($this->aliases[$alias->getName()]);
        }

        return isset($this->aliases[$alias]);
    }

    private function loadCore()
    {
        $this->get('Router')->loadRoutes();
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
