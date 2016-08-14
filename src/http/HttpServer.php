<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\http;


use Kawaii;
use kawaii\base\BaseServer;
use kawaii\web\Context;
use kawaii\web\Request;
use kawaii\web\Response;
use Swoole\Server;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends BaseServer
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
        static::$swooleServer->on('PipeMessage', [$this, 'onPipeMessage']);
        static::$swooleServer->on('Task', [$this, 'onTask']);
        static::$swooleServer->on('Finish', [$this, 'onFinish']);
        static::$swooleServer->on('WorkerError', [$this, 'onWorkerError']);
    }

    /**
     * @param Server $server
     * @param int $clientId
     * @param int $fromId
     * @param string $data
     */
    public function onReceive(Server $server, $clientId, $fromId, $data)
    {
        try {
            $this->handleOnReceive($clientId, $fromId, $data);
        } catch (\Exception $e) {
            echo "Exception occurred: {$e->getMessage()}\n";

            $response = (new Response())
                ->withStatus(500)
                ->withBody(StreamHelper::createStream('Exception occurred: ' . $e->getMessage() . ', Line: ' . $e->getLine() . '<hr /> Trace:<br /> ' . $e->getTraceAsString()));

            $server->send($clientId, (string)$response);
        }
        finally {
            $server->close($clientId);
        }
    }


    /**
     * @param Server $server
     * @param int $fromWorkerId
     * @param string $data
     */
    public function onPipeMessage(Server $server, $fromWorkerId, $data)
    {

    }

    /**
     * @param string Server $server
     * @param int $taskId
     * @param int $fromId
     * @param mixed $data
     */
    public function onTask(Server $server, $taskId, $fromId, $data)
    {
    }

    /**
     * @param Server $server
     * @param int $taskId
     * @param mixed $data
     */
    public function onFinish(Server $server, $taskId, $data)
    {

    }

    /**
     * @param Server $server
     * @param int $workerId
     * @param int $workerPid
     * @param int $exitCode
     */
    public function onWorkerError(Server $server, $workerId, $workerPid, $exitCode)
    {
        echo __FILE__ . ' error occurred.';
    }


    /**
     * @param int $clientId
     * @param int $fromId
     * @param string $data
     */
    protected function handleOnReceive($clientId, $fromId, $data)
    {
        if (isset(static::$buffers[$clientId])) {
            static::$buffers[$clientId] .= $data;
        } else {
            static::$buffers[$clientId] = $data;
        }

        $result = $this->validateRequest($clientId);
        if (is_int($result)) {
            return;
        }

        $context = static::handleRequest($result);
        $this->getSwooleServer()->send($clientId, (string)$context->response);

        unset(static::$buffers[$clientId]);
        unset($context, $result);
    }

    /**
     * @param Request $request
     * @return Context
     */
    protected static function handleRequest(Request $request)
    {
        return Kawaii::$app->handleRequest($request);
    }

    /**
     * @param int $clientId
     * @return int|Request
     */
    public static function validateRequest($clientId)
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
    public static function validateHeader($clientId)
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
    public static function validatePost($clientId, Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $contentLength = (int)$request->getContentLength();
            if ($contentLength < 0) {
                echo "No have Content-Length header\n";
                return self::TRANSFER_ERROR;
            }

            if ($contentLength > static::$settings['post_max_size']) {
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