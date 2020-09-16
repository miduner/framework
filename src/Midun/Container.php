<?php

namespace Midun;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Midun\Eloquent\Model;
use Midun\Http\FormRequest;
use Midun\Http\Exceptions\AppException;

class Container
{
    /**
     * Version of the application
     * 
     * @var string
     */
    const VERSION = "0.1.0";

    /**
     * Instance of the application
     * @var self
     */
    private static self $instance;

    /**
     * List of bindings instances
     * 
     * @var array
     */
    private array $instances = [];

    /**
     * Base path of the installation
     * @var string
     */
    private string $basePath;

    /**
     * Storage saving registry variables
     * @var array $storage
     */
    private array $storage = [];

    /**
     * Storage saving bindings objects
     * @var array $bindings
     */
    private array $bindings = [];

    /**
     * List of resolved bindings
     */
    private array $resolved = [];

    /**
     * Flag check should skip middleware
     */
    private bool $shouldSkipMiddleware = false;

    /**
     * Initial of container
     * 
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        $this->instance('path.route', $this->getRoutePath());
        $this->instance('path.cache', $this->getCachePath());
        $this->instance('path.config', $this->getConfigPath());
        $this->instance('path.public', $this->getPublicPath());
        $this->instance('path.storage', $this->getStoragePath());
        $this->instance('path.database', $this->getDatabasePath());

        self::$instance = $this;
    }

    /**
     * Get instance of container
     * 
     * @return self
     */
    public static function getInstance(): Container
    {
        if (!self::$instance) {
            return new self(...func_get_args());
        }

        return self::$instance;
    }

    /**
     * Get public path
     * 
     * @return string
     */
    private function getPublicPath(): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * Get cache path
     * 
     * @return string
     */
    private function getCachePath(): string
    {
        return $this->getStoragePath() . DIRECTORY_SEPARATOR . 'cache';;
    }

    /**
     * Get config path
     * 
     * @return string
     */
    private function getConfigPath(): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Get storage path
     * 
     * @return string
     */
    private function getStoragePath(): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * Get database path
     * 
     * @return string
     */
    private function getDatabasePath(): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'database';
    }

    /**
     * Get routing path
     * 
     * @return string
     */
    private function getRoutePath(): string
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes';
    }

    /**
     * Get base path of installation
     * 
     * @param string $path
     */
    public function basePath(string $path = ''): string
    {
        return !$path ? $this->basePath : $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Register instance of things
     * 
     * @param string $key
     * @param mixed $instance
     */
    private function instance(string $key, $instance): void
    {
        $this->instances[$key] = $instance;
    }

    /**
     * Register instance of things
     * 
     * @param string $key
     * @param mixed $instance
     */
    private function hasInstance(string $key): bool
    {
        return isset($this->instances[$key]);
    }

    /**
     *
     * Make a entity
     * @param string $entity
     * @return mixed
     * 
     * @throws \Midun\Http\Exceptions\AppException
     */
    public function resolve($entity)
    {
        if (!$this->canResolve($entity)) {
            throw new AppException("Cannot resolve entity `{$entity}`.\nIt's has not binding yet.");
        }

        $object = $this->build($entity);

        if (
            $this->bound($entity)
            && $this->takeBound($entity)['shared'] === true
            && !$this->isResolved($entity)
        ) {
            $this->putToResolved($entity, $object);
        }

        return $object;
    }

    /**
     * Put to resolved
     * 
     * @param string $abstract
     * @param mixed $concrete
     * 
     * @return void
     */
    private function putToResolved(string $abstract, $concrete): void
    {
        if ($this->isResolved($abstract)) {
            throw new AppException("Duplicated abstract resolve `{$abstract}`");
        }

        $this->resolved[$abstract] = $concrete;
    }

    /**
     * Check is resolved
     * 
     * @param string $abstract
     * 
     * @return bool
     */
    private function isResolved(string $abstract): bool
    {
        return isset($this->resolved[$abstract]);
    }

    /**
     * Check can resolve
     * 
     * @param mixed $entity
     * 
     * @return boolean
     * 
     * @throws AppException
     */
    private function canResolve($entity): bool
    {
        return $this->bound($entity) || class_exists($entity) || $this->hasInstance($entity);
    }

    /**
     *
     * Make a entity
     * @param string $entity
     * @return mixed
     */
    public function make($entity)
    {
        return isset($this->instances[$entity])
            ? $this->instances[$entity]
            : $this->resolve($entity);
    }

    /**
     * Register a concrete to abstract
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function singleton($abstract, $concrete): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Binding abstract to classes
     * @param string $abstract
     * @param string $concrete
     * @param bool $shared
     *
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param  string  $concrete
     * @return \Closure
     */
    private function getClosure(string $concrete): \Closure
    {
        return function () use ($concrete) {
            return $this->build($concrete);
        };
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  mixed  $concrete
     * 
     * @return mixed
     *
     * @throws AppException
     */
    public function build($concrete)
    {
        if (is_string($concrete) && $this->resolved($concrete)) {
            return $this->takeResolved($concrete);
        }

        if ($concrete instanceof Closure) {
            return call_user_func($concrete, $this);
        }
        if ($this->bound($concrete)) {
            return $this->build(
                $this->takeBound($concrete)['concrete']
            );
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new AppException("Class {$concrete} is not an instantiable !");
        }
        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        $instances = $this->resolveConstructorDependencies($dependencies);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Take bound dependencies
     * 
     * @param string $concrete
     * 
     * @return mixed
     */
    private function takeBound(string $concrete)
    {
        return $this->bindings[$concrete];
    }

    /**
     * Take resolved dependencies
     * 
     * @param string $concrete
     * 
     * @return mixed
     */
    private function takeResolved(string $concrete)
    {
        return $this->resolved[$concrete];
    }

    /**
     * Check is resolved
     * @param string $concrete
     *
     * @return boolean
     */
    private function resolved(string $abstract): bool
    {
        return isset($this->resolved[$abstract]);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $dependencies
     * @return array
     */
    private function resolveConstructorDependencies(array $dependencies): array
    {
        $array = [];
        foreach ($dependencies as $dependency) {
            if ($dependency->getClass() instanceof \ReflectionClass) {
                $object = $dependency->getClass()->getName();
                $array[$dependency->getName()] = $this->make($object);
            }
        }

        return $array;
    }

    /**
     * Resolve list of dependencies from options
     * 
     * @param string $controller
     * @param string $methodName
     * @param array $params
     *
     * @return array
     * 
     * @throws AppException
     */
    public function resolveMethodDependencyWithParameters($controller, $methodName, $params): array
    {
        try {
            $ref = new ReflectionMethod($controller, $methodName);
            $listParameters = $ref->getParameters();
            $array = [];
            foreach ($listParameters as $key => $parameter) {
                switch(true) {
                    case $parameter->getClass() instanceof \ReflectionClass:
                        $object = $this->buildStacks(
                            $parameter->getClass()->getName()
                        );
                        if($object instanceof Model) {
                            $arg = array_shift($params);
                            if (!$arg) {
                                throw new AppException("Missing parameter `{$parameter->getName()}` for initial model `{$parameter->getClass()->getName()}`");
                            }
                            $object = $object->findOrFail($arg);
                        }
                        $array = [...$array, $object];
                        break;
                    case is_null($parameter->getClass()):
                        $param = array_shift($params);
                        try {
                            $default = $parameter->getDefaultValue();
                        }catch(\ReflectionException $e) {
                            $default = null;
                        }
                        
                        if(!is_null($parameter->getType())) {
                                switch($parameter->getType()->getName()) {
                                    case 'int':
                                    case 'integer':
                                        $param = (int) $param ?: (is_numeric($default) ? $default : $default);
                                        break;
                                    case 'array':
                                        $param = (array) $param ?: $default;
                                        break;
                                    case 'object':
                                        $param = (object) $param ?: $default;
                                        break;
                                    case 'float':
                                        $param = (float) $param ?: $default;
                                        break;
                                    case 'string':
                                        $param = (string) $param ?: $default;
                                        break;
                                    case 'boolean':
                                    case 'bool':
                                            $param = (bool) $param ?: $default;
                                    break;
                                }
                        }

                        $array = [...$array, $param];
                        break;
                    default:
                        throw new AppException("Invalid type of parameter");

                }
            }
            return $array;
        } catch (ReflectionException $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * !! Only using in this class !!
     * Handle validation for request
     * @param string $object
     *
     * @return object
     * 
     * @throws AppException
     */
    private function buildStacks($object): object
    {
        try {
            $object = $this->build($object);
            if ($object instanceof FormRequest) {
                $object->executeValidate();
            }
            return $object;
        } catch (\ArgumentCountError $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * Get list of bindings
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * In bindings
     * 
     * @param string $entity
     * 
     * @return bool
     */
    private function bound(string $entity): bool
    {
        return isset($this->bindings[$entity]);
    }

    /**
     * Check is down for maintenance
     * 
     * @return bool
     */
    public function isDownForMaintenance(): bool
    {
        return false;
    }

    /**
     * Should skip global middlewares
     * 
     * @return bool
     */
    public function shouldSkipMiddleware(): bool
    {
        return $this->shouldSkipMiddleware;
    }

    /**
     * Get OS specific
     * 
     * @return string
     */
    public function getOS(): string
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'):
                return 'macosx';
            case stristr(PHP_OS, 'WIN'):
                return 'windows';
            case stristr(PHP_OS, 'LINUX'):
                return 'linux';
            default:
                return 'unknown';
        }
    }

    /**
     * Check is windows system
     * 
     * @return bool
     */
    public function isWindows(): bool
    {
        return "windows" === $this->getOs();
    }

    /**
     * Check is windows system
     * 
     * @return bool
     */
    public function isMacos(): bool
    {
        return "macosx" === $this->getOs();
    }

    /**
     * Check is windows system
     * 
     * @return bool
     */
    public function isLinux(): bool
    {
        return "linux" === $this->getOs();
    }

    /**
     * Check is windows system
     * 
     * @return bool
     */
    public function unknownOs(): bool
    {
        return "unknown" === $this->getOs();
    }
}
