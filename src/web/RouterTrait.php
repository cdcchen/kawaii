<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 15:50
 */

namespace kawaii\web;


/**
 * Class RouterTrait
 * @package kawaii\web
 */
trait RouterTrait
{
    /**
     * @param string $methods
     * @param string $path
     * @param callable $handler
     * @param bool $strict
     * @param string $suffix
     * @return $this|static
     */
    abstract public function addRoute(string $methods, string $path, callable $handler, bool $strict = false, string $suffix = '');

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function all(string $path, callable $handler)
    {
        return $this->addRoute('*', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function get(string $path, callable $handler)
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function post(string $path, callable $handler)
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function put(string $path, callable $handler)
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function head(string $path, callable $handler)
    {
        return $this->addRoute('HEAD', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function delete(string $path, callable $handler)
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function options(string $path, callable $handler)
    {
        return $this->addRoute('OPTIONS', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function patch(string $path, callable $handler)
    {
        return $this->addRoute('PATCH', $path, $handler);
    }
}