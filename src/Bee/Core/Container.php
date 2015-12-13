<?php
namespace Bee\Core;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use Bee\Exception\CoreException;

class Container implements ArrayAccess
{
    protected static $instance = null;

    protected $instances = [];

    protected function __construct()
    {
        //throw new CoreException('Can\'t instance it!');
    }

    public static function getInstance()
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     *
     * @param string $concrete
     * @return object
     * @throws CoreException
     */
    public function build($concrete, array $parameters = [])
    {
        // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
        if ($concrete instanceof Closure) {
            // 执行闭包函数，并将结果返回
            $this->instances[$concrete] = $concrete($this, $parameters);

            return $this->instances[$concrete];
        }

        /** @var ReflectionClass $reflector */
        $reflector = new ReflectionClass($concrete);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new CoreException("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $concrete;
        }

        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $dependencies = $constructor->getParameters();

        $parameters = $this->keyParametersByArgument(
            $dependencies, $parameters
        );

        // 递归解析构造函数的参数
        $instances = $this->getDependencies(
            $dependencies, $parameters
        );

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        $this->instances[$concrete] = $reflector->newInstanceArgs($instances);

        return $this->instances[$concrete];
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array $parameters
     * @param  array $primitives
     * @return array
     */
    protected function getDependencies(array $parameters, array $primitives = [])
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            if (array_key_exists($parameter->name, $primitives)) {
                $dependencies[] = $primitives[$parameter->name];
            } elseif (!empty($primitives)) {
                $dependencies[] = $primitives;
            } elseif (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array)$dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws CoreException
     */
    protected function resolveNonClass(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

        throw new CoreException($message);
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param  \ReflectionParameter $parameter
     * @return mixed
     *
     * @throws \Bee\Exception\CoreException
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->build($parameter->getClass()->name);
        }

            // If we can not resolve the class instance, we will check to see if the value
            // is optional, and if it is we will return the optional parameter value as
            // the value of the dependency, similarly to how we do this with scalars.
        catch (CoreException $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     * If extra parameters are passed by numeric ID, rekey them by argument name.
     *
     * @param  array $dependencies
     * @param  array $parameters
     * @return array
     */
    protected function keyParametersByArgument(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (is_numeric($key)) {
                unset($parameters[$key]);

                $parameters[$dependencies[$key]->name] = $value;
            }
        }

        return $parameters;
    }

    public function offsetExists($offset)
    {
        return isset($this->instances[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->instances[$offset] ?: null;
    }

    public function offsetSet($offset, $value)
    {
        $this->instances[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->instances[$offset]);
    }

    public function __set($name, $value)
    {
        $this->instances[$name] = $value;
    }

    public function __get($name)
    {
        return $this->build($this->instances[$name]);
    }
}