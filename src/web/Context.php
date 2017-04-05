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
     * @var \kawaii\server\HttpServerRequestHandleInterface|Application
     */
    public $app;
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
     * @param Application $app
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $config
     */
    public function __construct(
        Application $app,
        RequestInterface $request,
        ResponseInterface $response,
        array $config = []
    ) {
        $this->app = $app;
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