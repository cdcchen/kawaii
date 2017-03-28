<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/31
 * Time: 10:11
 */

namespace kawaii\web;


class RouteQueue implements \Countable
{
    /**
     * @var Route[][]
     */
    protected $staticRoutes = [];
    /**
     * @var Route[][]
     */
    protected $variableRoutes = [];

    public function add(Route $route): void
    {
        if ($route->isStatic()) {
            $this->addStatic($route);
        } else {
            $this->addVariable($route);
        }
    }

    private function addStatic(Route $route): void
    {
        $path = $route->data[0];
        $this->staticRoutes[$route->method][$path] = $route;
    }

    private function addVariable(Route $route): void
    {
        $this->variableRoutes[$route->method][$route->getRegex()] = $route;
    }

    public function hasStatic(string $method, string $path): bool
    {
        $method = strtoupper($method);
        return isset($this->staticRoutes[$method][$path]) || isset($this->staticRoutes['*'][$path]);
    }

    public function hasVariable(string $method): bool
    {
        $method = strtoupper($method);
        return isset($this->variableRoutes[$method]) || isset($this->variableRoutes['*']);
    }

    /**
     * @param string $method
     * @param string $path
     * @return Route|null
     */
    public function getStatic(string $method, string $path): ?Route
    {
        $routes = $this->getStatics($method);
        return empty($routes[$path]) ? null : $routes[$path];
    }

    /**
     * @param string $method
     * @return Route[]|array
     */
    public function getStatics(string $method): array
    {
        $method = strtoupper($method);
        $routes = $this->staticRoutes[$method] ?? [];
        if (isset($this->staticRoutes['*'])) {
            $routes = array_merge($this->staticRoutes['*'], $routes);
        }

        return $routes;
    }

    /**
     * @param string $method
     * @return Route[]|array
     */
    public function getVariables(string $method): array
    {
        $method = strtoupper($method);
        $routes = $this->variableRoutes[$method] ?? [];
        if (isset($this->variableRoutes['*'])) {
            $routes = array_merge($this->variableRoutes['*'], $routes);
        }

        return $routes;
    }

    /**
     * @return int
     * @todo 计算方式不正确
     */
    public function count(): int
    {
        return count($this->staticRoutes) + count($this->variableRoutes);
    }
}