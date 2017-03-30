<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\server;


use Closure;
use Kawaii;
use kawaii\base\ApplicationInterface;
use kawaii\base\BaseTask;
use kawaii\base\InvalidConfigException;
use kawaii\base\Object;
use Swoole\Server as SwooleServer;
use UnexpectedValueException;

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
     * @var string config file
     */
    protected $configFile;
    /**
     * @var array swoole server setting
     */
    protected $config = [];

    /**
     * @var \Swoole\Server
     */
    protected $swoole;

    /**
     * @var callable
     */
    protected $masterStartHandle;
    /**
     * @var callable
     */
    protected $masterStopHandle;
    /**
     * @var callable
     */
    protected $managerStartHandle;
    /**
     * @var callable
     */
    protected $managerStopHandle;
    /**
     * @var callable
     */
    protected $workerStartHandle;
    /**
     * @var callable
     */
    protected $workerStopHandle;
    /**
     * @var callable
     */
    protected $workerErrorHandle;
    /**
     * @var callable
     */
    protected $connectHandle;
    /**
     * @var callable
     */
    protected $closeHandle;
    /**
     * @var callable
     */
    protected $receiveHandle;
    /**
     * @var callable
     */
    protected $packetHandle;
    /**
     * @var callable
     */
    protected $pipeMessageHandle;
    /**
     * @var callable
     */
    protected $taskHandle;
    /**
     * @var callable
     */
    protected $finishHandle;

    /**
     * BaseServer constructor.
     * @param null|string $configFile
     * @param array $config
     */
    public function __construct(string $configFile = null, array $config = [])
    {
        parent::__construct($config);

        $this->configFile = $configFile;
        $this->loadConfig();

        $listener = static::$listeners[0] ?? static::defaultListener();
        $this->swoole = static::createSwooleServer($listener);

        static::testLogFile();
        static::testPidFile();

        if (isset($this->config['setting'])) {
            $this->swoole->set($this->config['setting']);
        } else {
            echo "User default setting.\n";
            // @todo use default setting
        }

        $this->setDefaultCallback();
        $this->setCallback();
    }

    /**
     * @throws InvalidConfigException
     */
    protected function loadConfig(): void
    {
        if (empty($this->configFile)) {
            return;
        }

        if (is_readable($this->configFile)) {
            $this->config = require($this->configFile);
        } else {
            throw new InvalidConfigException("Config file: {$this->configFile} is not exist or unreadable.");
        }
    }

    /**
     * @param Listener $listener
     * @param PortServer|null $server
     * @return Base|static
     */
    public function listen(Listener $listener, PortServer $server = null): self
    {
        static::$listeners[] = $listener;

        $port = $this->swoole->listen($listener->host, $listener->port, $listener->type);
        if ($server !== null) {
            $server->run($port);
        }

        return $this;
    }

    /**
     * @param ApplicationInterface $app
     * @return bool
     */
    public function run(ApplicationInterface $app): bool
    {
        $app->run();

        $this->bindCallback();

        $this->beforeRun();
        return $this->swoole->start();
    }

    /**
     *
     */
    protected function setCallback(): void
    {
    }

    /**
     * @param Listener $listener
     * @return mixed
     */
    abstract protected static function createSwooleServer(Listener $listener): SwooleServer;

    /**
     * Before server run
     */
    private function beforeRun(): void
    {
    }

    /**
     * @param string|null $name
     * @return array
     */
    public function getSetting(string $name = null): array
    {
        if ($name === null) {
            return $this->swoole->setting;
        } else {
            return $this->swoole->setting[$name] ?? null;
        }
    }

    /**
     * @return Listener
     */
    private static function defaultListener(): Listener
    {
        return new Listener(self::DEFAULT_HOST, self::DEFAULT_PORT, self::DEFAULT_TYPE, self::DEFAULT_MODE);
    }

    /**
     * Init log file
     */
    private function testLogFile(): void
    {
        $logFile = $this->getSetting('log_file');
        if ($logFile === null) {
            return;
        }

        $dir = dirname($logFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0644, true);
        } elseif (!is_writable($dir)) {
            echo "{$dir} is not writable.\n";
        } elseif (!is_writable($logFile)) {
            echo "{$logFile} is not writable.\n";
        }
    }

    /**
     * Init log file
     */
    private function testPidFile(): void
    {
        $pidFile = $this->getSetting('pid_file');
        if ($pidFile === null) {
            return;
        }

        $dir = dirname($pidFile);
        if (!file_exists($dir)) {
            mkdir($dir, 0644, true);
        } elseif (!is_writable($dir)) {
            echo "{$dir} is not writable.\n";
        } elseif (!is_writable($pidFile)) {
            echo "{$pidFile} is not writable.\n";
        }
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
        @cli_set_process_title($title);
    }

    /**
     * @param BaseTask $task
     * @param int $workerId
     * @return bool
     */
    public function asyncTask(BaseTask $task, $workerId = -1)
    {
        return $this->swoole->task($task, $workerId);
    }

    /**
     * @param BaseTask $task
     * @param float $timeout
     * @param int $workerId
     * @return mixed
     */
    public function syncTask(BaseTask $task, float $timeout = 0.5, int $workerId = -1)
    {
        return $this->swoole->taskwait($task, $timeout, $workerId);
    }

    /**
     * @param BaseTask[] $tasks
     * @param float $timeout
     * @return mixed
     */
    public function syncTaskMulti(array $tasks, float $timeout = 0.5)
    {
        return $this->swoole->taskWaitMulti($tasks, $timeout);
    }


    /**
     * @param callable $callback
     */
    public function onStart(callable $callback): void
    {
        $this->masterStartHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onStop(callable $callback): void
    {
        $this->masterStopHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onManagerStart(callable $callback): void
    {
        $this->managerStartHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onManagerStop(callable $callback): void
    {
        $this->managerStopHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkerStart(callable $callback): void
    {
        $this->workerStartHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkStop(callable $callback): void
    {
        $this->workerStopHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkerError(callable $callback): void
    {
        $this->workerErrorHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onConnect(callable $callback): void
    {
        $this->connectHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onClose(callable $callback): void
    {
        $this->closeHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onReceive(callable $callback): void
    {
        $this->receiveHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onPacket(callable $callback): void
    {
        $this->packetHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onPipeMessage(callable $callback): void
    {
        $this->pipeMessageHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onTask(callable $callback): void
    {
        $this->taskHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onFinish(callable $callback): void
    {
        $this->finishHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }


    /**
     * Bind Swoole server event callback
     */
    protected function bindCallback(): void
    {
        if (is_callable($this->masterStartHandle)) {
            $this->swoole->on('Start', $this->masterStartHandle);
        } elseif ($this->masterStartHandle !== null) {
            throw new UnexpectedValueException('masterStartHandle callback is not callable.');
        }

        if (is_callable($this->masterStopHandle)) {
            $this->swoole->on('Shutdown', $this->masterStopHandle);
        } elseif ($this->masterStopHandle !== null) {
            throw new UnexpectedValueException('masterStopHandle callback is not callable.');
        }

        if (is_callable($this->managerStartHandle)) {
            $this->swoole->on('ManagerStart', $this->managerStartHandle);
        } elseif ($this->managerStartHandle !== null) {
            throw new UnexpectedValueException('managerStartHandle callback is not callable.');
        }

        if (is_callable($this->managerStopHandle)) {
            $this->swoole->on('ManagerStop', $this->managerStopHandle);
        } elseif ($this->managerStopHandle !== null) {
            throw new UnexpectedValueException('managerStopHandle callback is not callable.');
        }

        if (is_callable($this->workerStartHandle)) {
            $this->swoole->on('WorkerStart', $this->workerStartHandle);
        } elseif ($this->workerStartHandle !== null) {
            throw new UnexpectedValueException('workerStartHandle callback is not callable.');
        }

        if (is_callable($this->workerStopHandle)) {
            $this->swoole->on('WorkerStop', $this->workerStopHandle);
        } elseif ($this->workerStopHandle !== null) {
            throw new UnexpectedValueException('workerStopHandle callback is not callable.');
        }

        if (is_callable($this->workerErrorHandle)) {
            $this->swoole->on('WorkerError', $this->workerErrorHandle);
        } elseif ($this->workerErrorHandle !== null) {
            throw new UnexpectedValueException('workerErrorHandle callback is not callable.');
        }

        if (is_callable($this->connectHandle)) {
            $this->swoole->on('Connect', $this->connectHandle);
        } elseif ($this->connectHandle !== null) {
            throw new UnexpectedValueException('connectHandle callback is not callable.');
        }

        if (is_callable($this->closeHandle)) {
            $this->swoole->on('Close', $this->closeHandle);
        } elseif ($this->closeHandle !== null) {
            throw new UnexpectedValueException('closeHandle callback is not callable.');
        }

        if (is_callable($this->receiveHandle)) {
            $this->swoole->on('Receive', $this->receiveHandle);
        } elseif ($this->receiveHandle !== null) {
            throw new UnexpectedValueException('receiveHandle callback is not callable.');
        }

        if (is_callable($this->packetHandle)) {
            $this->swoole->on('Packet', $this->packetHandle);
        } elseif ($this->packetHandle !== null) {
            throw new UnexpectedValueException('packetHandle callback is not callable.');
        }

        if (is_callable($this->pipeMessageHandle)) {
            $this->swoole->on('PipeMessage', $this->pipeMessageHandle);
        } elseif ($this->pipeMessageHandle !== null) {
            throw new UnexpectedValueException('pipeMessageHandle callback is not callable.');
        }

        if (is_callable($this->taskHandle)) {
            $this->swoole->on('Task', $this->taskHandle);
        } elseif ($this->taskHandle !== null) {
            throw new UnexpectedValueException('taskHandle callback is not callable.');
        }

        if (is_callable($this->finishHandle)) {
            $this->swoole->on('Finish', $this->finishHandle);
        } elseif ($this->finishHandle !== null) {
            throw new UnexpectedValueException('finishHandle callback is not callable.');
        }
    }

    /**
     * Set default callback
     */
    protected function setDefaultCallback(): void
    {
        $this->masterStartHandle = function (SwooleServer $server): void {
            static::setProcessName('master process');
            echo "Master pid: {$server->master_pid} starting...\n";
        };

        $this->masterStopHandle = function (SwooleServer $server): void {
            unlink(static::getPidFile());

            echo "Master pid: {$server->master_pid} shutdown...\n";
        };

        $this->managerStartHandle = function (SwooleServer $server): void {
            static::setProcessName('manager');

            echo "Manager pid: {$server->manager_pid} starting...\n";
        };

        $this->managerStopHandle = function (SwooleServer $server): void {
            echo "Manager pid: {$server->manager_pid} stopped...\n";
        };

        $this->workerStartHandle = function (SwooleServer $server, int $workId): void {
            static::setProcessName($server->taskworker ? 'task' : 'worker');

            // @todo 需要重新载入配置

            echo ($server->taskworker ? 'task' : 'worker') . ": $workId starting...\n";
        };

        $this->workerStopHandle = function (SwooleServer $server, int $workId): void {
            echo "Worker: $workId stopped...\n";
        };

        $this->workerErrorHandle = function (
            SwooleServer $server,
            int $workerId,
            int $workerPid,
            int $exitCode,
            int $signal
        ): void {
            echo "Worker error: id {$workerId}, pid {$workerPid}, exit code {$exitCode}, signal: {$signal}.\n";
        };

        $this->connectHandle = function (SwooleServer $server, int $fd, int $reactorId): void {
            echo "Client {$fd} form reactor {$reactorId} connected.\n";
        };

        $this->closeHandle = function (SwooleServer $server, int $fd, int $reactorId): void {
            echo "Client {$fd} from reactor {$reactorId} disconnected.\n";
        };

        $this->taskHandle = function (SwooleServer $server, int $taskId, int $fromWorkerId, $data): void {
            if ($data instanceof BaseTask) {
                $data->handle($server, $taskId);
            }

            echo "Task {$taskId} starting, worker {$fromWorkerId}.\n";
        };

        $this->finishHandle = function (SwooleServer $server, int $taskId, $data): void {
            if ($data instanceof BaseTask) {
                $data->done();
            }

            echo "Task {$taskId} run finished.\n";
        };

        $this->pipeMessageHandle = function (SwooleServer $server, int $fromWorkerId, $message): void {
            echo "Receive message: {$message} from worker {$fromWorkerId}.\n";
        };

        $this->receiveHandle = function (SwooleServer $server, int $fd, int $fromWorkerId, string $data): void {
            echo "Receive data: {$data} from client {$fd}, worker {$fromWorkerId}.\n";
        };

        $this->packetHandle = function (SwooleServer $server, string $data, array $client): void {
            $fd = unpack('L', pack('N', ip2long($client['address'])))[1];
            $fromId = ($client['server_socket'] << 16) + $client['port'];
            $server->send($fd, "I had received data: {$data}", $fromId);

            echo "Receive UDP data: {$data} from {$fd}.\n";
        };
    }
}