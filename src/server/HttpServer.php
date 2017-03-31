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
    protected $requestCallback;
    protected $requestHandle;

    public function onRequest(callable $callback): void
    {
        $this->requestCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    public function http(callable $callback)
    {
        $this->requestHandle = $callback;
        return $this;
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
        if (is_callable($this->requestCallback)) {
            $this->swoole->on('Request', $this->requestCallback);
        } else {
            throw new UnexpectedValueException('requestCallback is not callable.');
        }

        if (!is_callable($this->requestHandle)) {
            throw new UnexpectedValueException('requestHandle is not callable.');
        }

        parent::bindCallback();
    }

    protected function setCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;

        $this->requestCallback = function (SwooleHttpRequest $req, SwooleHttpResponse $res): void {
            try {
                $request = new Request(
                    $req->server['request_method'],
                    $req->server['request_uri'],
                    new HeaderCollection(empty($req->header) ? [] : $req->header),
                    $req->rawContent(),
                    '1.1',
                    $req->server
                );
                $request = $request->withQueryParams($req->get ?? [])
                                   ->withCookieParams($req->cookie ?? []);
                if (isset($req->post)) {
                    $request = $request->withParsedBody($req->post ?? []);
                }
                if (isset($req->files)) {
                    $request = $request->withUploadedFiles(static::buildUploadedFiles($req->files));
                }

                $response = call_user_func($this->requestHandle, $request, $this);

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

    /**
     * @param array $files
     * @return array
     */
    protected static function buildUploadedFiles(array $files): array
    {
        return [];
    }
}