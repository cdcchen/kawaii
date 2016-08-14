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
    protected static $_server;

    public function __construct(array $settings = [])
    {
        static::$settings = array_merge(static::$defaultSettings, $settings);
        $this->init();
    }

    public function run()
    {
        $server = $this->getSwooleServer();
        $server->on('Start', [$this, 'onServerStart']);
        $server->on('Shutdown', [$this, 'onServerShutdown']);
        $server->on('Connect', [$this, 'onConnect']);
        $server->on('Close', [$this, 'onClose']);
        $server->on('ManagerStart', [$this, 'onManagerStart']);
        $server->on('ManagerStop', [$this, 'onManagerStop']);
        $server->on('WorkerStart', [$this, 'onWorkerStart']);
        $server->on('WorkerStop', [$this, 'onWorkerStop']);

        $this->bindCallback();
        return $server->start();
    }

    abstract protected function bindCallback();

    protected function init()
    {
    }

    protected function getSwooleServer()
    {
        if (!is_object(static::$_server)) {
            static::$_server = new Server(
                static::$settings['host'],
                static::$settings['port'],
                static::$settings['mode'],
                static::$settings['type']
            );
            static::$_server->set(static::$settings);
        }

        return static::$_server;
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