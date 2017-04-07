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
use kawaii\base\BaseTask;
use kawaii\base\InvalidConfigException;
use kawaii\base\Object;
use Swoole\Server as SwooleServer;
use UnexpectedValueException;

/**
 * Class Server
 * @package kawaii\base
 *
 * @property \Traversable $connections
 */
abstract class BaseServer extends Object
{

    /**
     * Default server listener
     */
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 9527;
    const DEFAULT_MODE = SWOOLE_PROCESS;
    const DEFAULT_TYPE = SWOOLE_SOCK_TCP;

    /**
     * @var Listener[]
     */
    public static $listeners = [];
    /**
     * @var string config file
     */
    public $configFile;
    /**
     * @var array swoole server setting
     */
    public $config = [];

    /**
     * @var \Swoole\Server
     */
    protected $swoole;

    /**
     * @var callable
     */
    protected $masterStartCallback;
    /**
     * @var callable
     */
    protected $masterStopCallback;
    /**
     * @var callable
     */
    protected $managerStartCallback;
    /**
     * @var callable
     */
    protected $managerStopCallback;
    /**
     * @var callable
     */
    protected $workerStartCallback;
    /**
     * @var callable
     */
    protected $workerStopCallback;
    /**
     * @var callable
     */
    protected $workerErrorCallback;
    /**
     * @var callable
     */
    protected $connectCallback;
    /**
     * @var callable
     */
    protected $closeCallback;
    /**
     * @var callable
     */
    protected $receiveCallback;
    /**
     * @var callable
     */
    protected $packetCallback;
    /**
     * @var callable
     */
    protected $pipeMessageCallback;
    /**
     * @var callable
     */
    protected $taskCallback;
    /**
     * @var callable
     */
    protected $finishCallback;

    /**
     * BaseServer constructor.
     * @param null|string $configFile
     * @param array $config
     */
    public function __construct(string $configFile = null, array $config = [])
    {
        Kawaii::$server = $this;

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
     * @return BaseServer|static
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
     * @return bool
     */
    public function start(): bool
    {
        $this->beforeStart();
        $this->bindCallback();

        return $this->swoole->start();
    }

    /**
     * @return bool
     */
    public function shutdown(): bool
    {
        return $this->swoole->shutdown();
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
    protected function beforeStart(): void
    {
    }

    /**
     * @param string|null $name
     * @return array
     */
    public function getSetting(string $name = null): ?array
    {
        if ($name === null) {
            return $this->swoole->setting;
        } else {
            return $this->swoole->setting[$name] ?? null;
        }
    }

    /**
     * @return SwooleServer|\Swoole\Http\Server|\Swoole\WebSocket\Server
     */
    public function getSwoole(): SwooleServer
    {
        return $this->swoole;
    }

    /**
     * @param int $fd
     * @param int $fromId
     * @param bool $ignoreClose
     * @return Connection|null
     */
    public function getConnection(int $fd, int $fromId = -1, bool $ignoreClose = false): ? Connection
    {
        $data = $this->getSwoole()->connection_info($fd, $fromId, $ignoreClose);
        if (is_array($data)) {
            return new Connection($data);
        }
        return null;
    }

    /**
     * @return array|\Traversable
     */
    public function getConnections()
    {
        return $this->getSwoole()->connections;
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
    public static function getProcessName(): string
    {
        global $argv;
        return "php {$argv[0]}";
    }

    /**
     * @param string $extra
     */
    public static function setProcessName(string $extra): void
    {
        $title = static::getProcessName() . ' - ' . $extra;
        @cli_set_process_title($title);
    }

    /**
     * @param BaseProcess $process
     * @return bool
     */
    public function addProcess(BaseProcess $process)
    {
        return $process->run($this);
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
        $this->masterStartCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onStop(callable $callback): void
    {
        $this->masterStopCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onManagerStart(callable $callback): void
    {
        $this->managerStartCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onManagerStop(callable $callback): void
    {
        $this->managerStopCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkerStart(callable $callback): void
    {
        $this->workerStartCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkStop(callable $callback): void
    {
        $this->workerStopCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onWorkerError(callable $callback): void
    {
        $this->workerErrorCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onConnect(callable $callback): void
    {
        $this->connectCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onClose(callable $callback): void
    {
        $this->closeCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onReceive(callable $callback): void
    {
        $this->receiveCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onPacket(callable $callback): void
    {
        $this->packetCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onPipeMessage(callable $callback): void
    {
        $this->pipeMessageCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onTask(callable $callback): void
    {
        $this->taskCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onFinish(callable $callback): void
    {
        $this->finishCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }


    /**
     * Bind Swoole server event callback
     */
    protected function bindCallback(): void
    {
        if (is_callable($this->masterStartCallback)) {
            $this->swoole->on('Start', $this->masterStartCallback);
        } elseif ($this->masterStartCallback !== null) {
            throw new UnexpectedValueException('masterStartCallback is not callable.');
        }

        if (is_callable($this->masterStopCallback)) {
            $this->swoole->on('Shutdown', $this->masterStopCallback);
        } elseif ($this->masterStopCallback !== null) {
            throw new UnexpectedValueException('masterStopCallback is not callable.');
        }

        if (is_callable($this->managerStartCallback)) {
            $this->swoole->on('ManagerStart', $this->managerStartCallback);
        } elseif ($this->managerStartCallback !== null) {
            throw new UnexpectedValueException('managerStartCallback is not callable.');
        }

        if (is_callable($this->managerStopCallback)) {
            $this->swoole->on('ManagerStop', $this->managerStopCallback);
        } elseif ($this->managerStopCallback !== null) {
            throw new UnexpectedValueException('managerStopCallback is not callable.');
        }

        if (is_callable($this->workerStartCallback)) {
            $this->swoole->on('WorkerStart', $this->workerStartCallback);
        } elseif ($this->workerStartCallback !== null) {
            throw new UnexpectedValueException('workerStartCallback is not callable.');
        }

        if (is_callable($this->workerStopCallback)) {
            $this->swoole->on('WorkerStop', $this->workerStopCallback);
        } elseif ($this->workerStopCallback !== null) {
            throw new UnexpectedValueException('workerStopCallback is not callable.');
        }

        if (is_callable($this->workerErrorCallback)) {
            $this->swoole->on('WorkerError', $this->workerErrorCallback);
        } elseif ($this->workerErrorCallback !== null) {
            throw new UnexpectedValueException('workerErrorCallback is not callable.');
        }

        if (is_callable($this->connectCallback)) {
            $this->swoole->on('Connect', $this->connectCallback);
        } elseif ($this->connectCallback !== null) {
            throw new UnexpectedValueException('connectCallback is not callable.');
        }

        if (is_callable($this->closeCallback)) {
            $this->swoole->on('Close', $this->closeCallback);
        } elseif ($this->closeCallback !== null) {
            throw new UnexpectedValueException('closeCallback is not callable.');
        }

        if (is_callable($this->receiveCallback)) {
            $this->swoole->on('Receive', $this->receiveCallback);
        } elseif ($this->receiveCallback !== null) {
            throw new UnexpectedValueException('receiveCallback is not callable.');
        }

        if (is_callable($this->packetCallback)) {
            $this->swoole->on('Packet', $this->packetCallback);
        } elseif ($this->packetCallback !== null) {
            throw new UnexpectedValueException('packetCallback is not callable.');
        }

        if (is_callable($this->pipeMessageCallback)) {
            $this->swoole->on('PipeMessage', $this->pipeMessageCallback);
        } elseif ($this->pipeMessageCallback !== null) {
            throw new UnexpectedValueException('pipeMessageCallback is not callable.');
        }

        if (is_callable($this->taskCallback)) {
            $this->swoole->on('Task', $this->taskCallback);
        } elseif ($this->taskCallback !== null) {
            throw new UnexpectedValueException('taskCallback is not callable.');
        }

        if (is_callable($this->finishCallback)) {
            $this->swoole->on('Finish', $this->finishCallback);
        } elseif ($this->finishCallback !== null) {
            throw new UnexpectedValueException('finishCallback is not callable.');
        }
    }

    /**
     * Set default callback
     */
    protected function setDefaultCallback(): void
    {
        $handle = new DefaultHandle($this);

        $this->masterStartCallback = [$handle, 'onMasterStart'];
        $this->masterStopCallback = [$handle, 'onMasterStop'];
        $this->managerStartCallback = [$handle, 'onManagerStart'];
        $this->managerStopCallback = [$handle, 'onManagerStop'];
        $this->workerStartCallback = [$handle, 'onWorkerStart'];
        $this->workerStopCallback = [$handle, 'onWorkerStop'];
        $this->workerErrorCallback = [$handle, 'onWorkerError'];
        $this->connectCallback = [$handle, 'onConnect'];
        $this->closeCallback = [$handle, 'onClose'];
        $this->taskCallback = [$handle, 'onTask'];
        $this->finishCallback = [$handle, 'onFinish'];
        $this->pipeMessageCallback = [$handle, 'onPipeMessage'];
        $this->receiveCallback = [$handle, 'onReceive'];
        $this->packetCallback = [$handle, 'onPacket'];
    }
}