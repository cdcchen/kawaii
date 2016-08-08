<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/30
 * Time: 17:08
 */

namespace kawaii\web;

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
    protected function init()
    {
        $this->routes = new RouteQueue();
    }

    /**
     * @return RouteQueue
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @inheritdoc
     */
    public function addRoute($methods, $path, callable $handler, $strict = false, $suffix = '')
    {
        $methods = (array)$methods;
        if (!empty($suffix)) {
            $path .= '.' . ltrim($suffix, '.');
        }

        $routeData = RouteParser::parse(ltrim($path, '/'));
//        print_r($routeData);
        foreach ($routeData as $item) {
            foreach ($methods as $method) {
                $route = new Route($method, $handler, $item, $strict, $suffix);
                $this->routes->add($route);
            }
        }

        return $this;
    }

    /**
     * @param callable $handler
     */
    public function addDefaultRoute(callable $handler)
    {
        $this->addRoute('*', '{controller}/{action}', $handler);
//        $route = new Route('*', $handler, ['*']);
//        $this->routes->add($route);
    }

    /**
     * @param string $method
     * @param string $path
     * @return array|int
     */
    public function dispatch($method, $path)
    {
        if ($this->routes->hasStatic($method, $path)) {
            $route = $this->routes->getStatic($method, $path);
            return [self::ROUTE_FOUND, $route->handler, []];
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

                return [self::ROUTE_FOUND, $route->handler, $params];
            }
        }

        if ($this->routes->hasStatic($method, '*')) {
            $route = $this->routes->getStatic($method, '*');
            return [self::ROUTE_FOUND, $route->handler, []];
        }

        return [self::ROUTE_NOT_FOUND];
    }

    /**
     * @param $path
     */
    public function route($path)
    {

    }


    /**
     * @param $name
     * @param callable $callback
     */
    public function param($name, callable $callback)
    {

    }

    /**
     * @param $path
     * @return array
     */
    protected function parsePath($path)
    {
        return RouteParser::parse($path);
    }

    /**
     * @param $method
     * @param $params
     * @param $handler
     */
    protected function buildRoute($method, $params, $handler)
    {
    }
}