<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 17:55
 */

namespace kawaii\http;

use cdcchen\psr7\ServerRequest;
use kawaii\base\ApplicationInterface;
use kawaii\base\ContextInterface;
use kawaii\base\Object;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;


/**
 * Class Context
 * @package kawaii\web
 *
 * @property Application $app
 * @property Request $request
 * @property Response $response
 * @property SwooleHttpRequest $req
 * @property SwooleHttpResponse $res
 */
class Context extends Object implements ContextInterface
{
    /**
     * @var array
     */
    public $routeParams = [];
    /**
     * @var Response
     */
    public $response;

    /**
     * @var ApplicationInterface|Application
     */
    private $_app;
    /**
     * @var Request
     */
    private $_request;
    /**
     * @var SwooleHttpRequest
     */
    private $_req;
    /**
     * @var SwooleHttpResponse
     */
    private $_res;

    /**
     * Context constructor.
     * @param Application $app
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param SwooleHttpRequest $req
     * @param SwooleHttpResponse $res
     * @param array $config
     */
    public function __construct(
        Application $app,
        ServerRequestInterface $request,
        ResponseInterface $response,
        SwooleHttpRequest $req,
        SwooleHttpResponse $res,
        array $config = []
    ) {
        $this->_app = $app;
        $this->_request = $request;
        $this->response = $response;
        $this->_req = $req;
        $this->_res = $res;

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
     * @return SwooleHttpRequest
     */
    public function getReq(): SwooleHttpRequest
    {
        return $this->_req;
    }

    /**
     * @return SwooleHttpResponse
     */
    public function getRes(): SwooleHttpResponse
    {
        return $this->_res;
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