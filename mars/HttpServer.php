<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 15:42
 */

namespace mars;


use Kawaii;
use kawaii\base\ApplicationInterface;
use Swoole\Http\Server;

/**
 * Class HttpServer
 * @package mars
 */
class HttpServer extends Server
{
    /**
     * @var string
     */
    public $hookNamespace = 'app\\hooks';

    /**
     * @param HandleInterface $app
     */
    public function run(HandleInterface $app): void
    {
        if ($app instanceof ApplicationInterface) {
            $app->prepare();
        }

        $callback = new Callback();
        $callback->setRequestHandle($app)->bind($this);

        $this->start();
    }

    /**
     * @param string $configFile
     * @param string $settingFile
     * @return static
     */
    public static function create(string $configFile = null, string $settingFile = null): self
    {
        if ($configFile && !is_readable($configFile)) {
            echo "Server config file is not exist or unreadable.\n";
            exit(1);
        }
        if ($settingFile && !is_readable($settingFile)) {
            echo "Swoole server setting file is not exist or unreadable.\n";
            exit(1);
        }

        if ($configFile) {
            $config = require($configFile);
            $host = $config['host'] ?? Listener::DEFAULT_HOST;
            $port = $config['port'] ?? Listener::DEFAULT_PORT;
            $type = $config['type'] ?? Listener::DEFAULT_TYPE;
            $mode = $config['mode'] ?? Listener::DEFAULT_MODE;
            $listener = new Listener($host, $port, $type, $mode);
        } else {
            $listener = Listener::default();
        }
        $server = new static($listener->host, $listener->port, $listener->mode, $listener->type);

        if ($settingFile) {
            $server->set(require($settingFile));
        }

        if (isset($config)) {
            Kawaii::configure($server, $config);
        }

        echo "Server listen on {$listener->host}:{$listener->port}.\n";
        return $server;
    }

    /**
     * @param int $fd
     * @return null|Connection
     */
    public function getConnection(int $fd): ?Connection
    {
        $info = $this->connection_info($fd);
        if (is_array($info)) {
            return new Connection($fd, $info);
        }

        return null;
    }

    /**
     * @param string $name
     * @param string|null $defaultValue
     * @return mixed|null
     */
    public function getSetting(string $name, $defaultValue = null)
    {
        return $this->setting[$name] ?? $defaultValue;
    }

    /**
     * @return string
     */
    public function getProcessName(): string
    {
        global $argv;
        return "php {$argv[0]}";
    }

    /**
     * @param string $extra
     */
    public function setProcessName(string $extra): void
    {
        $title = static::getProcessName() . ' - ' . $extra;
        @cli_set_process_title($title);
    }

    /**
     * @param string $className
     * @return null|WorkerHookInterface
     */
    public function createHook(string $className): ?WorkerHookInterface
    {
        $className = ltrim($this->hookNamespace . '\\' . $className, '\\');
        if (!class_exists($className)) {
            return null;
        }
        return Kawaii::createObject($className);
    }
}