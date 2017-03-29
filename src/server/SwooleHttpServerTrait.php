<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/27
 * Time: 22:31
 */

namespace kawaii\server;


use cdcchen\psr7\HeaderCollection;
use Kawaii;
use kawaii\base\InvalidConfigException;
use kawaii\web\Request;
use kawaii\web\Response;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

trait SwooleHttpServerTrait
{
    use SwooleServerTrait;

    /**
     * @var callable
     */
    protected $onRequest;

    /**
     * @param callable $callback
     */
    public function setRequestHandle(callable $callback): void
    {
        $this->onRequest = $callback;
    }

    /**
     * @inheritdoc
     */
    private function bindHttpCallback(): void
    {
        $this->onConnect = $this->onReceive = null;
        $requestHandle = $this->onRequest ?: [$this, 'onRequestHandle'];

        if (is_callable($requestHandle)) {
            $this->on('Request', $requestHandle);
        } else {
            throw new InvalidConfigException('onRequest callback must be callable.');
        }
    }

    /**
     * @param SwooleHttpRequest $req
     * @param SwooleHttpResponse $res
     */
    public function onRequestHandle(SwooleHttpRequest $req, SwooleHttpResponse $res): void
    {
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
            $response = (new Response(500, null, $e->getMessage()));
            echo "Exception occurred: {$e->getMessage()}\n";
        }

        $res->status($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            $res->header($name, $value);
        }
        /* @var \cdcchen\psr7\Cookie $cookie */
        foreach ($response->getCookies() as $cookie) {
            $res->cookie($cookie->name, $cookie->name, $cookie->expires, $cookie->path, $cookie->domain,
                $cookie->secure, $cookie->httpOnly);
        }
        $res->end((string)$response->getBody());
    }
}