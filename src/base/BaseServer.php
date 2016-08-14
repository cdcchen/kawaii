<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\base;


use Swoole\Server;

/**
 * Class BaseServer
 * @package kawaii\base
 */
abstract class BaseServer
{
    /**
     * @var array
     */
    public static $defaultSettings = [
        'host' => 'localhost',
        'port' => '9502',
        'mode' => SWOOLE_PROCESS,
        'type' => SWOOLE_SOCK_TCP,
    ];

    /**
     * @var array
     */
    protected static $settings;

    /**
     * @var \kawaii\web\Request[]
     */
    protected $requests = [];

    /**
     * @var \Swoole\Server
     */
    protected static $swooleServer;

    public function __construct(array $settings = [])
    {
        static::$settings = array_merge(static::$defaultSettings, $settings);
        $this->init();
    }

    public function run()
    {
        static::initSwooleServer();
        static::$swooleServer->on('Start', [$this, 'onServerStart']);
        static::$swooleServer->on('Shutdown', [$this, 'onServerShutdown']);
        static::$swooleServer->on('Connect', [$this, 'onConnect']);
        static::$swooleServer->on('Close', [$this, 'onClose']);
        static::$swooleServer->on('ManagerStart', [$this, 'onManagerStart']);
        static::$swooleServer->on('ManagerStop', [$this, 'onManagerStop']);
        static::$swooleServer->on('WorkerStart', [$this, 'onWorkerStart']);
        static::$swooleServer->on('WorkerStop', [$this, 'onWorkerStop']);

        $this->bindCallback();
        return static::$swooleServer->start();
    }

    abstract protected function bindCallback();

    protected function init()
    {
    }

    protected static function initSwooleServer()
    {
        static::$swooleServer = new Server(
            static::$settings['host'],
            static::$settings['port'],
            static::$settings['mode'],
            static::$settings['type']
        );
        static::$swooleServer->set(static::$settings);
    }

    public function onServerStart(Server $server)
    {
        echo "Server starting...\n";
    }

    public function onServerShutdown(Server $server)
    {
        echo "Server shutdown...\n";
    }

    public function onConnect(Server $server, $clientId, $fromId)
    {
        echo "Client: $clientId connected.\n";
    }

    public function onClose(Server $server, $clientId, $fromId)
    {
        unset($this->requests[$clientId]);
        $memory = memory_get_usage() . '/' . memory_get_usage(true) . ' - ' . memory_get_peak_usage() . '/' . memory_get_peak_usage(true);
        echo "Client: $clientId disconnected.\n{$memory}\n-----------------------------\n";
    }

    public function onManagerStart(Server $server)
    {

    }

    public function onManagerStop(Server $server)
    {

    }

    public function onWorkerStart(Server $server, $workId)
    {

    }

    public function onWorkerStop(Server $server, $workId)
    {

    }
}