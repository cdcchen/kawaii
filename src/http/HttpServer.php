<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\http;


use Kawaii;
use kawaii\base\Server1;
use kawaii\web\Request;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends Server1
{
    const TRANSFER_ERROR    = -1;
    const TRANSFER_WAIT     = 1;
    const TRANSFER_FINISHED = 2;

    /**
     * @inheritdoc
     */
    protected function bindCallback()
    {
        static::$swooleServer->on('Request', [$this, 'onRequest']);
    }

    /**
     * @param SwooleRequest $req
     * @param SwooleResponse $res
     */
    public function onRequest(SwooleRequest $req, SwooleResponse $res)
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
        /* @var \kawaii\http\Cookie $cookie */
        foreach ($response->getCookies() as $cookie) {
            $res->cookie($cookie->name, $cookie->name, $cookie->expires, $cookie->path, $cookie->domain,
                $cookie->secure, $cookie->httpOnly);
        }
        $res->end((string)$response->getBody());
    }
}