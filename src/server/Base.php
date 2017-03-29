<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\server;


use Kawaii;
use kawaii\base\ApplicationInterface;
use kawaii\base\BaseTask;
use kawaii\base\InvalidConfigException;
use kawaii\base\Object;
use Swoole\Server as SwooleServer;

/**
 * Class Server
 * @package kawaii\base
 */
abstract class Base extends Object
{
    /**
     * @var Listener[]
     */
    protected static $listeners = [];

    /**
     * Default server listener
     */
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 9527;
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
     * @var \Swoole\Server
     */
    protected static $swooleServer;

    /**
     * BaseServer constructor.
     * @param null|string $configFile
     * @param array $config
     */
    public function __construct(string $configFile = null, array $config = [])
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
     * @return static|self
     */
    public function listen(string $host = '0.0.0.0', int $port, int $type = SWOOLE_SOCK_TCP): self
    {
        static::$listeners[] = new Listener($host, $port, $type);
        return $this;
    }

    /**
     * @param ApplicationInterface $app
     * @return bool
     */
    public function run(ApplicationInterface $app): bool
    {
        static::initSwooleServer();

        static::$swooleServer->on('Start', [$this, 'onMasterStart']);
        static::$swooleServer->on('Shutdown', [$this, 'onMasterStop']);
        static::$swooleServer->on('ManagerStart', [$this, 'onManagerStart']);
        static::$swooleServer->on('ManagerStop', [$this, 'onManagerStop']);
        static::$swooleServer->on('WorkerStart', [$this, 'onWorkerStart']);
        static::$swooleServer->on('WorkerStop', [$this, 'onWorkerStop']);
        static::$swooleServer->on('WorkerError', [$this, 'onWorkerError']);
        static::$swooleServer->on('Close', [$this, 'onClose']);
        static::$swooleServer->on('Task', [$this, 'onTask']);
        static::$swooleServer->on('Finish', [$this, 'onFinish']);
        static::$swooleServer->on('PipeMessage', [$this, 'onPipeMessage']);

        $this->bindCallback();
        $app->run();

        return static::$swooleServer->start();
    }

    /**
     * Restart Swoole server
     * @ todo 未完成
     */
    protected static function restart(): void
    {
        static::$swooleServer->shutdown();
        static::loadConfig();
        static::initSwooleServer();
    }

    /**
     * Bind Swoole server event callback
     */
    abstract protected function bindCallback(): void;

    /**
     * @param Listener $listener
     * @return mixed
     */
    abstract static protected function createSwooleServer(Listener $listener): SwooleServer;

    /**
     * Init swoole server log file
     */
    private static function initSwooleServer(): void
    {
        $config = static::getSwooleConfig();

        if ($filename = $config['log_file']) {
            static::initSwooleLogFile($filename);
        }

        // init swoole_server
        $listener = empty(static::$listeners) ? static::defaultListener() : static::$listeners[0];
        static::$swooleServer = static::createSwooleServer($listener);
        for ($i = 1; $i < count(static::$listeners); $i++) {
            $listener = static::$listeners[$i];
            static::$swooleServer->addlistener($listener->host, $listener->port, $listener->type);
        }

        static::$swooleServer->set($config);

    }

    /**
     * @throws InvalidConfigException
     */
    protected static function loadConfig(): void
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
     * reload server config
     */
    protected static function reload(): void
    {
        static::loadConfig();
    }

    /**
     * @return array
     */
    private static function getSwooleConfig(): array
    {
        return static::$config['swoole'] ?? [];
    }

    /**
     * @return Listener
     */
    private static function defaultListener(): Listener
    {
        return new Listener(self::DEFAULT_HOST, self::DEFAULT_PORT, self::DEFAULT_TYPE, self::DEFAULT_MODE);
    }

    /**
     * @param string $filename
     */
    private static function initSwooleLogFile(string $filename): void
    {
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0644, true);
        }

        if ($handle = fopen($filename, 'a')) {
            fclose($handle);
            chmod($filename, 0644); // 需要判断返回值, 然后写日志。
        }
    }

    /**
     * @return string
     */
    protected static function getPidFile(): string
    {
        return static::$config['pid_file'] ?? (sys_get_temp_dir() . '/kawaii.pid');
    }

    /**
     * @return string
     */
    protected static function getProcessName(): string
    {
        global $argv;
        return "php {$argv[0]}";
    }

    /**
     * @param string $extra
     */
    protected static function setProcessName(string $extra): void
    {
        $title = static::getProcessName() . ' - ' . $extra;
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        } else {
            swoole_set_process_name($title);
        }
    }

    /**
     * @param BaseTask $task
     * @param int $workerId
     * @return bool
     */
    public function asyncTask(BaseTask $task, $workerId = -1)
    {
        return static::$swooleServer->task($task, $workerId);
    }

    /**
     * @param BaseTask $task
     * @param float $timeout
     * @param int $workerId
     * @return mixed
     */
    public function syncTask(BaseTask $task, float $timeout = 0.5, int $workerId = -1)
    {
        return static::$swooleServer->taskwait($task, $timeout, $workerId);
    }

    /**
     * @param BaseTask[] $tasks
     * @param float $timeout
     * @return mixed
     */
    public function syncTaskMulti(array $tasks, float $timeout = 0.5)
    {
        return static::$swooleServer->taskWaitMulti($tasks, $timeout);
    }


    /**
     * @param SwooleServer $server
     */
    public function onMasterStart(SwooleServer $server): void
    {
        file_put_contents(static::getPidFile(), $server->master_pid);
        static::setProcessName('master process');

        echo "Server pid: {$server->master_pid} starting...\n";
    }

    /**
     * @param SwooleServer $server
     */
    public function onMasterStop(SwooleServer $server): void
    {
        unlink(static::getPidFile());

        echo "Server pid: {$server->master_pid} shutdown...\n";
    }

    /**
     * @param SwooleServer $server
     */
    public function onManagerStart(SwooleServer $server): void
    {
        static::setProcessName('manager');

        echo "Manager pid: {$server->manager_pid} starting...\n";
    }

    /**
     * @param SwooleServer $server
     */
    public function onManagerStop(SwooleServer $server): void
    {
        echo "Manager pid: {$server->manager_pid} stopped...\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $workId
     */
    public function onWorkerStart(SwooleServer $server, int $workId): void
    {
        static::setProcessName($server->taskworker ? 'task' : 'worker');

        static::reload();
        Kawaii::$app->reload();

        echo ($server->taskworker ? 'task' : 'worker') . ": $workId starting...\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $workId
     */
    public function onWorkerStop(SwooleServer $server, int $workId): void
    {
        echo "Worker: $workId stopped...\n";
    }

    /**
     * @param SwooleServer $server
     * @param int $workerId
     * @param int $workerPid
     * @param int $exitCode
     */
    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode): void
    {
        echo __FILE__ . ' error occurred.';
    }

    /**
     * @param SwooleServer $server
     * @param int $clientId
     * @param int $fromId
     */
    public function onClose(SwooleServer $server, int $clientId, int $fromId): void
    {
        $memory = memory_get_usage() . '/' . memory_get_usage(true) . ' - ' . memory_get_peak_usage() . '/' . memory_get_peak_usage(true);
        echo "Client: $clientId disconnected.\n{$memory}\n-----------------------------\n";
    }

    /**
     * @param string SwooleServer $server
     * @param int $taskId
     * @param int $fromId
     * @param mixed $data
     * @return mixed
     */
    public function onTask(SwooleServer $server, int $taskId, int $fromId, $data): void
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
    public function onFinish(SwooleServer $server, int $taskId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->done();
        }

    }


    /**
     * @param SwooleServer $server
     * @param int $fromWorkerId
     * @param string $data
     */
    public function onPipeMessage(SwooleServer $server, int $fromWorkerId, $data): void
    {

    }
}