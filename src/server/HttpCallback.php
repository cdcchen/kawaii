<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:24
 */

namespace kawaii\server;


use cdcchen\psr7\HeaderCollection;
use cdcchen\psr7\Response;
use cdcchen\psr7\ServerRequest;
use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Server;

/**
 * Class HttpCallback
 * @package kawaii\server
 */
class HttpCallback extends BaseCallback
{
    /**
     * @var HttpHandleInterface
     */
    protected $requestHandle;

    public function setRequestHandle(HttpHandleInterface $handle)
    {
        $this->requestHandle = $handle;
        return $this;
    }

    /**
     * @param Server|HttpServer $server
     */
    public function bind(Server $server): void
    {
        parent::bind($server);

        if ($this->requestHandle instanceof HttpHandleInterface) {
            $server->on('Request', [$this, 'onRequest']);
        }
    }

    /**
     * @param SwooleHttpRequest $req
     * @param SwooleHttpResponse $res
     */
    public function onRequest(SwooleHttpRequest $req, SwooleHttpResponse $res): void
    {
        try {
            $request = static::buildServerRequest($req);

            // App handle request
            $response = $this->requestHandle->handleRequest($request, $req, $res);

            if (!($response instanceof Response)) {
                $response = static::buildServerErrorResponse('requestHandle must be return an instance of \cdcchen\psr7\Response');
            }
        } catch (\Exception $e) {
            $message = $e->getFile() . PHP_EOL . $e->getLine() . PHP_EOL . $e->getMessage();
            $response = new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $message);
            echo "Exception occurred: {$e->getMessage()}\n";
        }

        foreach ($response->getHeaders() as $name => $values) {
            $res->header($name, $response->getHeaderLine($name));
        }

        // @todo 上面已经输出header了，理论上不用再输出cookie了。
        /* @var \cdcchen\psr7\Cookie $cookie */
        foreach ($response->getCookies() as $cookie) {
            $res->cookie($cookie->name, $cookie->name, $cookie->expires, $cookie->path, $cookie->domain,
                $cookie->secure, $cookie->httpOnly);
        }
        $res->status($response->getStatusCode());
        $res->end((string)$response->getBody());
    }

    /**
     * @param SwooleHttpRequest $req
     * @return ServerRequest
     */
    public static function buildServerRequest(SwooleHttpRequest $req): ServerRequest
    {
        $request = new ServerRequest(
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
        };

        return $request;
    }


    /**
     * @param array $files
     * @return UploadedFileInterface[]
     */
    protected static function buildUploadedFiles(array $files): array
    {
        return [];
    }

    /**
     * @param string $message
     * @return ResponseInterface
     */
    protected static function buildServerErrorResponse($message): ResponseInterface
    {
        return new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $message);
    }
}