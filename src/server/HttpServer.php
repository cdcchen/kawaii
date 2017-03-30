<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use cdcchen\psr7\HeaderCollection;
use Closure;
use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use kawaii\web\Request;
use kawaii\web\Response;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;
use UnexpectedValueException;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends Base
{
    protected $requestHandle;

    public function onRequest(callable $callback): void
    {
        $this->requestHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    protected static function createSwooleServer(Listener $listener): SwooleServer
    {
        return new SwooleHttpServer($listener->host, $listener->port);
    }

    /**
     * @inheritdoc
     */
    protected function bindCallback(): void
    {
        if (is_callable($this->requestHandle)) {
            $this->swoole->on('Request', $this->requestHandle);
        } else {
            throw new UnexpectedValueException('onRequest callback is not callable.');
        }

        parent::bindCallback();
    }

    protected function setCallback(): void
    {
        $this->receiveHandle = $this->connectHandle = null;
        $this->setRequestHandle();
    }

    protected function setRequestHandle(): void
    {
        $this->requestHandle = function (SwooleHttpRequest $req, SwooleHttpResponse $res): void {
            try {
                $request = new Request(
                    $req->server['request_method'],
                    $req->server['request_uri'],
                    new HeaderCollection(empty($req->header) ? [] : $req->header),
                    $req->rawContent(),
                    '1.1',
                    $req->server
                );
                $request = $request->withQueryParams(empty($req->get) ? [] : $req->get)
                                   ->withCookieParams(empty($req->cookie) ? [] : $req->cookie);
                $context = Kawaii::$app->handleRequest($request);
                $response = $context->response;

            } catch (\Exception $e) {
                $response = (new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $e->getMessage()));
                echo "Exception occurred: {$e->getMessage()}\n";
            }

            $res->status($response->getStatusCode());

            foreach ($response->getHeaders() as $name => $value) {
                $res->header($name, $value);
            }
            $serverSignature = empty($this->config['server_signature']) ? 'Kawaii' : $this->config['server_signature'];
            $res->header('server', $serverSignature);

            /* @var \cdcchen\psr7\Cookie $cookie */
            foreach ($response->getCookies() as $cookie) {
                $res->cookie($cookie->name, $cookie->name, $cookie->expires, $cookie->path, $cookie->domain,
                    $cookie->secure, $cookie->httpOnly);
            }
            $res->end((string)$response->getBody());
        };
    }
}