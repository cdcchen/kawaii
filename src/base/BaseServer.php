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
abstract class BaseServer extends Object
{
    /**
     * @var ServerListener[]
     */
    protected static $listeners = [];

    /**
     * Default server listener
     */
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = '0.0.0.0';
    const DEFAULT_MODE = SWOOLE_PROCESS;
    const DEFAULT_TYPE = SWOOLE_SOCK_TCP;

    /**
     * @var string
     */
    protected static $configFile;
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

    /**
     * BaseServer constructor.
     * @param null|string $configFile
     * @param array $config
     */
    public function __construct($configFile = null, $config = [])
    {
        Kawaii::$server = $this;

        static::$configFile = $configFile;
        static::loadConfig();

        parent::__construct($config);
    }

    /**
     * @param int $port
     * @param string $host
     * @param int $type
     * @return $this
     */
    public function listen($port, $host = '0.0.0.0', $type = SWOOLE_SOCK_TCP)
    {
        static::$listeners[] = new ServerListener($port, $host, $type);
        return $this;
    }

    /**
     * @param ApplicationInterface $app
     * @return bool
     */
    public function run(ApplicationInterface $app)
    {
        $app->run();
        static::initSwooleServer();

        static::$swooleServer->on('Start', [$this, 'onMasterStart']);
        static::$swooleServer->on('Shutdown', [$this, 'onMasterStop']);
        static::$swooleServer->on('ManagerStart', [$this, 'onManagerStart']);
        static::$swooleServer->on('ManagerStop', [$this, 'onManagerStop']);
        static::$swooleServer->on('WorkerStart', [$this, 'onWorkerStart']);
        static::$swooleServer->on('WorkerStop', [$this, 'onWorkerStop']);
        static::$swooleServer->on('WorkerError', [$this, 'onWorkerError']);
        static::$swooleServer->on('Connect', [$this, 'onConnect']);
        static::$swooleServer->on('Close', [$this, 'onClose']);

        $this->bindCallback();
        return static::$swooleServer->start();
    }

    /**
     * Restart Swoole server
     * @ todo 未完成
     */
    protected static function restart()
    {
        static::$swooleServer->shutdown();
        static::loadConfig();
        static::initSwooleServer();
    }

    /**
     * Bind Swoole server event callback
     */
    abstract protected function bindCallback();

    /**
     * Init swoole server log file
     */
    private static function initSwooleServer()
    {
        $config = static::getSwooleConfig();

        if ($filename = $config['log_file']) {
            static::initSwooleLogFile($filename);
        }

        // init swoole_server
        $listeners = static::$listeners;
        $listener = empty($listeners) ? static::getDefaultListener() : $listeners[0];
        static::$swooleServer = new Server($listener->host, $listener->port, SWOOLE_PROCESS, $listener->type);
        for ($i = 1; $i < count($listeners); $i++) {
            $listener = $listeners[$i];
            static::$swooleServer->addlistener($listener->host, $listener->port, $listener->type);
        }

        static::$swooleServer->set($config);

    }

    /**
     * @throws InvalidConfigException
     */
    protected static function loadConfig()
    {
        if (empty(static::$configFile)) {
            return;
        }

        if (file_exists(static::$configFile)) {
            static::$config = require(static::$configFile);
        } else {
            $configFile = static::$configFile;
            throw new InvalidConfigException("Config file: {$configFile} is not exist.");
        }
    }

    /**
     * @return array
     */
    private static function getSwooleConfig()
    {
        return isset(static::$config['swoole']) ? static::$config['swoole'] : [];
    }

    /**
     * @return ServerListener
     */
    private static function getDefaultListener()
    {
        return new ServerListener(self::DEFAULT_PORT, self::DEFAULT_HOST, self::DEFAULT_TYPE, self::DEFAULT_MODE);
    }

    /**
     * @param string $filename
     */
    private static function initSwooleLogFile($filename)
    {
        $dirname = dirname($filename);
        if (!file_exists($dirname)) {
            mkdir($dirname, 0644, true);
        }

        if ($handle = fopen($filename, 'a')) {
            fclose($handle);
            chmod($filename, 0644); // 需要判断返回值, 然后写日志。
        }
    }

    protected static function getPidFile()
    {
        if (isset(static::$config['pid_file'])) {
            return static::$config['pid_file'];
        } else {
            return sys_get_temp_dir() . '/kawaii.pid';
        }
    }

    protected static function getProcessName()
    {
        global $argv;
        return "php {$argv[0]}";
    }

    protected static function setProcessName($extra)
    {
        $title = static::getProcessName() . ' - ' . $extra;
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } else {
            swoole_set_process_name($title);
        }
    }

    /**
     * @param Server $server
     */
    public function onMasterStart(Server $server)
    {
        file_put_contents(static::getPidFile(), $server->master_pid);
        static::setProcessName('Master');

        echo "Server pid: {$server->master_pid} starting...\n";
    }

    /**
     * @param Server $server
     */
    public function onMasterStop(Server $server)
    {
        unlink(static::getPidFile());

        echo "Server pid: {$server->master_pid} shutdown...\n";
    }

    /**
     * @param Server $server
     */
    public function onManagerStart(Server $server)
    {
        static::setProcessName('Manager');

        echo "Manager pid: {$server->manager_pid} starting...\n";
    }

    /**
     * @param Server $server
     */
    public function onManagerStop(Server $server)
    {
        echo "Manager pid: {$server->manager_pid} stopped...\n";
    }

    /**
     * @param Server $server
     * @param int $workId
     */
    public function onWorkerStart(Server $server, $workId)
    {
        static::setProcessName($server->taskworker ? 'Task' : 'Worker');

        static::loadConfig();
        Kawaii::$app->reload();

        echo ($server->taskworker ? 'Task' : 'Worker') . ": $workId starting...\n";
    }

    /**
     * @param Server $server
     * @param int $workId
     */
    public function onWorkerStop(Server $server, $workId)
    {
        echo "Worker: $workId stopped...\n";
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
     * @param Server $server
     * @param int $clientId
     * @param int $fromId
     */
    public function onConnect(Server $server, $clientId, $fromId)
    {
        echo "Client: $clientId connected.\n";
    }

    /**
     * @param Server $server
     * @param int $clientId
     * @param int $fromId
     */
    public function onClose(Server $server, $clientId, $fromId)
    {
        unset(static::$requests[$clientId]);
        $memory = memory_get_usage() . '/' . memory_get_usage(true) . ' - ' . memory_get_peak_usage() . '/' . memory_get_peak_usage(true);
        echo "Client: $clientId disconnected.\n{$memory}\n-----------------------------\n";
    }
}