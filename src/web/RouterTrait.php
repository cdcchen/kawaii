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
     * @param string|array $methods
     * @param string $path
     * @param callable $handler
     * @param bool $strict
     * @param string $suffix
     * @return $this
     */
    abstract public function addRoute($methods, $path, callable $handler, $strict = false, $suffix = '');

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function all($path, $handler)
    {
        return $this->addRoute('*', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function get($path, $handler)
    {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function post($path, $handler)
    {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function put($path, $handler)
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function head($path, $handler)
    {
        return $this->addRoute('HEAD', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function delete($path, $handler)
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function options($path, $handler)
    {
        return $this->addRoute('OPTIONS', $path, $handler);
    }

    /**
     * @param string $path
     * @param callable $handler
     * @return $this
     */
    public function patch($path, $handler)
    {
        return $this->addRoute('PATCH', $path, $handler);
    }
}