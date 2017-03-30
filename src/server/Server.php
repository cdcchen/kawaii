<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use Kawaii;
use kawaii\web\Request;
use kawaii\web\Response;
use Swoole\Server as SwooleServer;

/**
 * Class Server
 * @package kawaii\base
 * @todo 不使用swoole_http_server，自己处理onReceive接受来的数据
 */
class Server extends Base
{
    const TRANSFER_ERROR    = -1;
    const TRANSFER_WAIT     = 1;
    const TRANSFER_FINISHED = 2;

    /**
     * @var array
     */
    private static $buffers = [];

    static protected function createSwooleServer(Listener $listener): SwooleServer
    {
        return new SwooleServer($listener->host, $listener->port, SWOOLE_PROCESS, $listener->type);
    }


    /**
     * @inheritdoc
     */
    protected function bindCallback(): void
    {
        static::$swoole->on('Connect', [$this, 'onConnect']);
        static::$swoole->on('Receive', [$this, 'onReceive']);
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     */
    public function onConnect(SwooleServer $server, int $clientId, int $fromId): void
    {
        echo "Client: $clientId connected.\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     * @param string $data
     */
    public function onReceive(SwooleServer $server, int $clientId, int $fromId, $data): void
    {
        try {
            if (isset(static::$buffers[$clientId])) {
                static::$buffers[$clientId] .= $data;
            } else {
                static::$buffers[$clientId] = $data;
            }

            $result = $this->validateRequest($clientId);
            if (is_int($result)) {
                return;
            }

            $context = Kawaii::$app->handleRequest($result);
            static::$swoole->send($clientId, (string)$context->response);

            unset(static::$buffers[$clientId]);
            unset($context, $result);
        } catch (\Exception $e) {
            $response = (new Response(500, null, $e->getMessage()));

            $server->send($clientId, (string)$response);
            unset($response);

            echo "Exception occurred: {$e->getMessage()}\n";
        }
        finally {
            $server->close($clientId);
        }
    }

    /**
     * @param int $clientId
     * @return int|Request
     */
    private static function validateRequest(int $clientId)
    {
        $result = static::validateHeader($clientId);
        if ($result !== true) {
            return $result;
        }

        $request = Request::create(static::$buffers[$clientId]);
        if ($request->getMethod() === 'POST') {
            $result = static::validatePost($clientId, $request);
            if ($result !== self::TRANSFER_FINISHED) {
                return $result;
            }
        }

        return $request;
    }

    /**
     * @param int $clientId
     * @return bool|int
     */
    private static function validateHeader(int $clientId)
    {
        $data = static::$buffers[$clientId];
        if (strpos($data, Request::HTTP_EOF) === false) {
            return self::TRANSFER_WAIT;
        }

        return true;
    }

    /**
     * @param int $clientId
     * @param Request $request
     * @return int
     */
    private static function validatePost(int $clientId, Request $request): int
    {
        if ($request->getMethod() === 'POST') {
            $contentLength = (int)$request->getContentLength();
            if ($contentLength < 0) {
                echo "No have Content-Length header\n";
                return self::TRANSFER_ERROR;
            }

            if ($contentLength > static::$config['post_max_size']) {
                echo "Post data is too long.\n";
                return self::TRANSFER_ERROR;
            }

            if ($contentLength > strlen((string)$request->getBody())) {
                echo "Receiving data ....\n";
                return self::TRANSFER_WAIT;
            }
        }

        return self::TRANSFER_FINISHED;
    }
}