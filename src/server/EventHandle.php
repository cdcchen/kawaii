<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/27
 * Time: 13:24
 */

namespace kawaii\server;


use Kawaii;
use kawaii\base\BaseTask;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Frame;

class EventHandle
{
    /**
     * @param SwooleServer|SwooleServerTrait $server
     */
    public static function onMasterStart(SwooleServer $server): void
    {
        file_put_contents($server->getPidFile(), $server->master_pid);
        Base::setProcessName('master process');

        echo "Master {$server->master_pid} started.\n";
    }

    /**
     * @param SwooleServer|SwooleServerTrait $server
     */
    public static function onMasterStop(SwooleServer $server): void
    {
        unlink($server->getPidFile());

        echo "Master {$server->master_pid} stopped.\n";
    }

    /**
     * @param SwooleServer|SwooleServerTrait $server
     */
    public static function onManagerStart(SwooleServer $server): void
    {
        Base::setProcessName('manager');

        echo "Manager {$server->manager_pid} started.\n";
    }

    /**
     * @param SwooleServer|SwooleServerTrait $server
     */
    public static function onManagerStop(SwooleServer $server): void
    {
        echo "Manager {$server->manager_pid} stopped...\n";
    }

    /**
     * @param SwooleServer|SwooleServerTrait $server
     * @param int $workId
     */
    public static function onWorkerStart(SwooleServer $server, int $workId): void
    {
        Base::setProcessName($server->taskworker ? 'task' : 'worker');

        echo ($server->taskworker ? 'Task' : 'Worker') . "{$workId} {$server->worker_pid} started.\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $workId
     */
    public static function onWorkerStop(SwooleServer $server, int $workId): void
    {
        echo ($server->taskworker ? 'Task' : 'Worker') . "{$workId} {$server->worker_pid} stopped.\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $workerId
     * @param int $workerPid
     * @param int $exitCode
     */
    public static function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode): void
    {
        echo ($server->taskworker ? 'Task' : 'Worker') . "$workerId {$workerPid} error.\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     */
    public static function onConnect(SwooleServer $server, int $clientId, int $fromId): void
    {
        echo "Client {$clientId} connected.\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     */
    public static function onClose(SwooleServer $server, int $clientId, int $fromId): void
    {
        $memory = memory_get_usage() . '/' . memory_get_usage(true) . ' - ' . memory_get_peak_usage() . '/' . memory_get_peak_usage(true);
        echo "Client {$clientId} disconnected.\n{$memory}\n-----------------------------\n";
    }

    /**
     * @param string SwooleServer $server
     * @param int $taskId
     * @param int $fromId
     * @param mixed $data
     * @return mixed
     */
    public static function onTask(SwooleServer $server, int $taskId, int $fromId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->handle($server, $taskId);
        }

//        $dataText = var_export($data->getData(), true);
//        echo "Task: $taskId starting...\n Data: $dataText";
    }

    /**
     * @param SwooleServer $server
     * @param int $taskId
     * @param mixed $data
     */
    public static function onFinish(SwooleServer $server, int $taskId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->done();
        }
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     * @param mixed $data
     */
    public static function onReceive(SwooleServer $server, int $clientId, int $fromId, $data): void
    {
        echo "Received data: {$data}\n";
        $server->send($clientId, "Server received your data: {$data}\n");
    }

    public function onPacket(SwooleServer $server, string $data, array $clientInfo)
    {
        echo "Received data: {$data}\n";

        $fd = unpack('L', pack('N', ip2long($clientInfo['address'])))[1];
        $reactorId = ($clientInfo['server_socket'] << 16) + $clientInfo['port'];

        $server->send($fd, "Server received your data: {$data}\n", $reactorId);
    }

    /**
     * @param SwooleServer $server
     * @param int $fromWorkerId
     * @param string $data
     */
    public static function onPipeMessage(SwooleServer $server, int $fromWorkerId, $data): void
    {

    }


    #################### websocket default callback ##############################

    /**
     * @param WebsocketServer1 $server
     * @param SwooleHttpRequest $request
     */
    public static function onOpen(WebsocketServer1 $server, SwooleHttpRequest $request): void
    {
        echo "Websocket client connected\n";
    }

    /**
     * @param WebsocketServer1 $server
     * @param Frame $frame
     */
    public static function onMessage(WebsocketServer1 $server, Frame $frame): void
    {
        echo "Receive message: {
        $frame->data}\n";
    }
}