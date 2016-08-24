<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\http;


use Kawaii;
use kawaii\web\Request;
use kawaii\web\Response;
use Swoole\Server as SwooleServer;

/**
 * Class Server
 * @package kawaii\base
 */
class Server extends \kawaii\base\Server
{
    /**
     * Http head max length
     */
    const HTTP_HEAD_MAX_LENGTH = 8192;

    const TRANSFER_ERROR    = -1;
    const TRANSFER_WAIT     = 1;
    const TRANSFER_FINISHED = 2;

    /**
     * @var array
     */
    private static $buffers = [];

    /**
     * @inheritdoc
     */
    protected function bindCallback()
    {
        static::$swooleServer->on('Receive', [$this, 'onReceive']);
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     * @param string $data
     */
    public function onReceive(SwooleServer $server, $clientId, $fromId, $data)
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
            static::$swooleServer->send($clientId, (string)$context->response);

            unset(static::$buffers[$clientId]);
            unset($context, $result);
        } catch (\Exception $e) {
            $response = (new Response(500))->withBody(StreamHelper::createStream($e->getMessage()));

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
    private static function validateRequest($clientId)
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
    private static function validateHeader($clientId)
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
    private static function validatePost($clientId, Request $request)
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