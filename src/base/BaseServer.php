<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\base;


use Kawaii;
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
    public static $defaultConfig = [
        'swoole' => [
            'host' => '0.0.0.0',
            'port' => 9502,
            'mode' => SWOOLE_PROCESS,
            'type' => SWOOLE_SOCK_TCP,
        ],
    ];

    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var \kawaii\web\Request[]
     */
    protected static $requests = [];

    /**
     * @var \Swoole\Server
     */
    protected static $swooleServer;

    public function __construct(array $config = [])
    {
        Kawaii::$server = $this;
        static::$config = array_merge(static::$defaultConfig, $config);
        $this->init();
    }

    public function run(ApplicationInterface $app)
    {
        $app->run();
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
        $config = static::$config['swoole'];

        if ($filename = $config['log_file']) {
            static::initLogFile($filename);
        }

        static::$swooleServer = new Server($config['host'], $config['port'], $config['mode'], $config['type']);
        unset($config['host'], $config['port'], $config['mode'], $config['type']);
        static::$swooleServer->set($config);

    }

    private static function initLogFile($filename)
    {
        $dirname = dirname($filename);
        if (!file_exists($dirname)) {
            mkdir($dirname, 0755, true);
        }
        file_put_contents($filename, '', FILE_APPEND);
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
        unset(static::$requests[$clientId]);
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