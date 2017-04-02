<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 19:40
 */

namespace kawaii\server;


use cdcchen\psr7\HeaderCollection;
use cdcchen\psr7\Response;
use cdcchen\psr7\ServerRequest;
use Closure;
use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

/**
 * Class HttpServerTrait
 * @package kawaii\server
 */
trait HttpServerTrait
{
    /**
     * @var callable
     */
    protected $requestCallback;
    /**
     * @var callable
     */
    protected $requestHandle;

    /**
     * @param callable $callback Set \Swoole\Http\Server onRequest callback
     */
    public function onRequest(callable $callback): void
    {
        $this->requestCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * Set http onRequest callback
     */
    protected function setHttpCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;

        $this->requestCallback = function (SwooleHttpRequest $req, SwooleHttpResponse $res): void {
            try {
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
                }

                // App handle request
                $response = call_user_func($this->requestHandle, $request, $this);
                if (!($response instanceof Response)) {
                    $response = static::buildServerErrorResponse('requestHandle must be return an instance of \kawaii\web\Response');
                }
            } catch (\Exception $e) {
                $message = $e->getFile() . PHP_EOL . $e->getLine() . PHP_EOL . $e->getMessage();
                $response = new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $message);
                echo "Exception occurred: {$e->getMessage()}\n";
            }


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
            $res->status($response->getStatusCode());
            $res->end((string)$response->getBody());
        };
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