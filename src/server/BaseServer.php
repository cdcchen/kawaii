<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\server;


use Kawaii;
use kawaii\base\BaseTask;
use kawaii\base\InvalidConfigException;
use kawaii\base\Object;
use Swoole\Server as SwooleServer;

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
    public $listeners = [];
    /**
     * @var string config file
     */
    public $configFile;
    /**
     * @var array swoole server setting
     */
    public $config = [];
    /**
     * @var BaseServer[]
     */
    private $processes = [];
    /**
     * @var \Swoole\Server
     */
    private $swoole;

    /**
     * @var string|BaseCallback
     */
    protected $callback;


    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    abstract protected static function swooleServer(Listener $listener): SwooleServer;

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

        static::testLogFile();
        static::testPidFile();

        if (isset($this->config['setting'])) {
            $this->getSwoole()->set($this->config['setting']);
        } else {
            echo "User default setting.\n";
            // @todo use default setting
        }

        $this->callback = new $this->callback($this);
    }

    /**
     * @param Listener $listener
     * @param PortServer|null $server
     * @return BaseServer|static
     */
    public function listen(Listener $listener, PortServer $server = null): self
    {
        $this->listeners[] = $listener;

        $port = $this->getSwoole()->listen($listener->host, $listener->port, $listener->type);
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
        $this->callback->bind();
        $this->beforeStart();

        return $this->getSwoole()->start();
    }

    /**
     * @return bool
     */
    public function reload()
    {
        return $this->getSwoole()->reload();
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        return $this->getSwoole()->shutdown();
    }

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
            return $this->getSwoole()->setting;
        } else {
            return $this->getSwoole()->setting[$name] ?? null;
        }
    }

    /**
     * @return SwooleServer|\Swoole\Http\Server|\Swoole\WebSocket\Server
     */
    public function getSwoole(): SwooleServer
    {
        if (!is_object($this->swoole)) {
            $this->swoole = static::swooleServer($this->mainListener());
        };

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
    private function mainListener(): Listener
    {
        return new Listener(
            $this->config['host'] ?? self::DEFAULT_HOST,
            $this->config['port'] ?? self::DEFAULT_PORT,
            $this->config['type'] ?? self::DEFAULT_TYPE,
            $this->config['mode'] ?? self::DEFAULT_MODE
        );
    }

    /**
     * @throws InvalidConfigException
     */
    private function loadConfig(): void
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
        $this->processes[$process->getPid()] = $process;
        return $process->run($this);
    }

    /**
     * @param BaseTask $task
     * @param int $workerId
     * @return bool
     */
    public function asyncTask(BaseTask $task, $workerId = -1)
    {
        return $this->getSwoole()->task($task, $workerId);
    }

    /**
     * @param BaseTask $task
     * @param float $timeout
     * @param int $workerId
     * @return mixed
     */
    public function syncTask(BaseTask $task, float $timeout = 0.5, int $workerId = -1)
    {
        return $this->getSwoole()->taskwait($task, $timeout, $workerId);
    }

    /**
     * @param BaseTask[] $tasks
     * @param float $timeout
     * @return mixed
     */
    public function syncTaskMulti(array $tasks, float $timeout = 0.5)
    {
        return $this->getSwoole()->taskWaitMulti($tasks, $timeout);
    }

    /**
     * @param string $event
     * @param callable $callback
     */
    public function on(string $event, callable $callback): void
    {
        $this->getSwoole()->on($event, $callback);
    }
}