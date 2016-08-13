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
     * @var Application|\kawaii\web\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var \kawaii\web\Request[]
     */
    protected $requests = [];

    /**
     * @var \Swoole\Server
     */
    private $_server;

    public function __construct(array $settings = [])
    {
        $this->settings = array_merge(static::$defaultSettings, $settings);
        $this->init();
    }

    public function run(Application $app)
    {
        $this->app = $app;

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
        if (!is_object($this->_server)) {
            $this->_server = new Server(
                $this->settings['host'],
                $this->settings['port'],
                $this->settings['mode'],
                $this->settings['type']
            );
            $this->_server->set($this->settings);
        }

        return $this->_server;
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