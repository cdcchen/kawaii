<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/30
 * Time: 17:08
 */

namespace kawaii\http;

use kawaii\base\Object;


/**
 * Class Router
 * @package kawaii\web
 */
class Router extends Object
{
    use RouterTrait;

    /**
     * Route not found
     */
    const ROUTE_NOT_FOUND = -1;
    /**
     * route found
     */
    const ROUTE_FOUND = 1;

    /**
     * @var RouteQueue
     */
    private $routes;

    /**
     * init after __construct
     */
    protected function init(): void
    {
        $this->routes = new RouteQueue();
    }

    /**
     * @return RouteQueue
     */
    public function getRoutes(): RouteQueue
    {
        return $this->routes;
    }

    /**
     * @inheritdoc
     */
    public function addRoute(
        string $methods,
        string $path,
        callable $handle,
        bool $strict = false,
        string $suffix = ''
    ): self {
        if (!empty($suffix)) {
            $path .= '.' . ltrim($suffix, '.');
        }

        $routeData = RouteParser::parse($path);
//        print_r($routeData);
        foreach ($routeData as $item) {
            $methods = array_map('trim', explode(',', $methods));
            foreach ($methods as $method) {
                $route = new Route($method, $handle, $item, $strict, $suffix);
                $this->routes->add($route);
            }
        }

        return $this;
    }

    /**
     * @param callable $handler
     */
    public function addDefaultRoute(callable $handler): void
    {
        $this->addRoute('*', '{controller}/{action}', $handler);
    }

    /**
     * @param string $method
     * @param string $path
     * @return array|bool
     */
    public function dispatch(string $method, string $path)
    {
        if ($this->routes->hasStatic($method, $path)) {
            $route = $this->routes->getStatic($method, $path);
            return [$route->handler, []];
        }

        if ($this->routes->hasVariable($method)) {
            $routes = $this->routes->getVariables($method);

            foreach ($routes as $route) {
                if (!preg_match('~' . $route->getRegex() . '~', $path, $matches)) {
                    continue;
                }

                $params = [];
                $i = 0;
                foreach ($route->getVarNames() as $varName) {
                    $params[$varName] = $matches[++$i];
                }

                return [$route->handler, $params];
            }
        }

        if ($this->routes->hasStatic($method, '*')) {
            $route = $this->routes->getStatic($method, '*');
            return [$route->handler, []];
        }

        return false;
    }
}