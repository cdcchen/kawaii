<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\http;


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
    private $buffers = [];

    /**
     * @inheritdoc
     */
    protected function bindCallback()
    {
        $server = $this->getSwooleServer();

        $server->on('Receive', [$this, 'onReceive']);
        $server->on('PipeMessage', [$this, 'onPipeMessage']);
        $server->on('Task', [$this, 'onTask']);
        $server->on('Finish', [$this, 'onFinish']);
        $server->on('WorkerError', [$this, 'onWorkerError']);
    }

    /**
     * @param Server $server
     * @param int $clientId
     * @param int $fromId
     * @param string $data
     */
    public function onReceive(Server $server, $clientId, $fromId, $data)
    {
        $dataLength = strlen($data);
        echo "Waiting receive data from $clientId, data length: {$dataLength} ...\n";

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
        if (isset($this->buffers[$clientId])) {
            $this->buffers[$clientId] .= $data;
        } else {
            $this->buffers[$clientId] = $data;
        }

        $result = $this->validateRequest($clientId);
        switch ($result) {
            case self::TRANSFER_ERROR:
            case self::TRANSFER_WAIT;
                return;
            default:
                break;
        }

        $context = $this->handleRequest($result);
        $this->getSwooleServer()->send($clientId, (string)$context->response);

        unset($this->requests[$clientId], $this->buffers[$clientId]);
        unset($context, $result);
    }

    /**
     * @param Request $request
     * @return Context
     */
    protected function handleRequest(Request $request)
    {
        return $this->app->handleRequest($request);
    }

    /**
     * @param int $clientId
     * @return bool|int
     */
    public function validateRequest($clientId)
    {
        $result = $this->validateHeader($clientId);
        if ($result !== true) {
            return $result;
        }

        return $this->validatePost($clientId);
    }

    /**
     * @param int $clientId
     * @return bool|int
     */
    public function validateHeader($clientId)
    {
        $data = $this->buffers[$clientId];
        if (strpos($data, Request::HTTP_EOF) === false) {
            return self::TRANSFER_WAIT;
        }

        return true;
    }

    /**
     * @param int $clientId
     * @return int|Request
     */
    public function validatePost($clientId)
    {
        $request = Request::create($this->buffers[$clientId]);

        if ($request->getMethod() === 'POST') {
            $contentLength = (int)$request->getContentLength();
            if ($contentLength < 0) {
                echo "No have Content-Length header\n";
                return self::TRANSFER_ERROR;
            }

            if ($contentLength > $this->settings['post_max_size']) {
                echo "Post data is too long.\n";
                return self::TRANSFER_ERROR;
            }

            if ($contentLength > strlen((string)$request->getBody())) {
                echo "Receiving data ....\n";
                return self::TRANSFER_WAIT;
            }
        }

        return $request;
    }
}