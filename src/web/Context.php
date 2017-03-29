<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 17:55
 */

namespace kawaii\web;

use kawaii\base\Object;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * Class Context
 * @package kawaii\web
 */
class Context extends Object
{
    /**
     * @var Request
     */
    public $request;
    /**
     * @var Response
     */
    public $response;

    /**
     * @var array
     */
    public $routeParams = [];

    /**
     * Context constructor.
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $config
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, array $config = [])
    {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($config);
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed|null
     */
    public function getRouteParam(string $name, $defaultValue = null)
    {
        return $this->routeParams[$name] ?? $defaultValue;
    }
}