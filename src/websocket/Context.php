<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 17:55
 */

namespace kawaii\websocket;

use cdcchen\psr7\ServerRequest;
use kawaii\base\ApplicationInterface;
use kawaii\base\ContextInterface;
use kawaii\base\Object;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class Context
 * @package kawaii\websocket
 *
 * @property Application $app
 * @property ServerRequest $request
 */
class Context extends Object implements ContextInterface
{
    /**
     * @var Application
     */
    public $_app;
    /**
     * @var ServerRequestInterface
     */
    public $_request;
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
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param array $config
     */
    public function __construct(Application $app, ServerRequestInterface $request, Response $response, array $config = [])
    {
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
     * @return ServerRequest|RequestInterface
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