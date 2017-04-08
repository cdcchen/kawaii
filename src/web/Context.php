<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 17:55
 */

namespace kawaii\web;

use cdcchen\psr7\ServerRequest;
use kawaii\base\ApplicationInterface;
use kawaii\base\ContextInterface;
use kawaii\base\Object;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * Class Context
 * @package kawaii\web
 *
 * @property Application $app
 * @property Request $request
 */
class Context extends Object implements ContextInterface
{
    /**
     * @var Response
     */
    public $response;
    /**
     * @var \kawaii\server\HttpHandleInterface|Application
     */
    private $_app;
    /**
     * @var Request
     */
    private $_request;

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
        $this->_app = $app;
        $this->_request = $request;
        $this->response = $response;

        parent::__construct($config);
    }

    /**
     * @return ApplicationInterface|Application
     */
    public function getApp(): ApplicationInterface
    {
        return $this->_app;
    }

    /**
     * @return ServerRequest|Request|RequestInterface
     */
    public function getRequest(): ServerRequest
    {
        return $this->_request;
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