<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/7
 * Time: 12:56
 */

namespace kawaii\http;


use kawaii\base\Object;

/**
 * Class RouteRule
 * @package kawaii\web
 */
class RouteRule extends Object
{
    /**
     * @var array
     */
    public $method = ['*'];
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $route;
    /**
     * @var bool
     */
    public $strict = false;
    /**
     * @var string
     */
    public $suffix = '';

    /**
     * RouteRule constructor.
     * @param array $path
     * @param string $route
     * @param array $config
     */
    public function __construct($path, $route, array $config = [])
    {
        $this->path = trim($path);
        $this->route = trim($route);

        if (empty($path) || empty($route)) {
            throw new \InvalidArgumentException('Path and Route cannot be empty.');
        }

        parent::__construct($config);

        if (empty($this->method)) {
            $this->method = ['*'];
        }
        $this->method = (array)$this->method;
    }

    /**
     * @param array $config
     * @return static
     */
    public static function create(array $config)
    {
        $path = $config['path'];
        $route = $config['route'];
        unset($config['path'], $config['route']);

        return new static($path, $route, $config);
    }
}