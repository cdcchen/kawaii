<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 15:42
 */

namespace kawaii\server;


use Kawaii;
use Swoole\Http\Server;

/**
 * Class HttpServer
 * @package kawaii\server
 */
class HttpServer extends Server
{
    /**
     * @var string
     */
    public $hookNamespace = 'app\\hooks';

    /**
     * @param HandleInterface $app
     * @return $this
     */
    public function run(HandleInterface $app)
    {
        $callback = new Callback();
        $callback->setRequestHandle($app)->bind($this);

        return $this;
    }

    /**
     * @param array $config
     * @return static
     */
    public static function create(array $config)
    {
        $host = $config['host'] ?? Listener::DEFAULT_HOST;
        $port = $config['port'] ?? Listener::DEFAULT_PORT;
        $type = $config['type'] ?? Listener::DEFAULT_TYPE;
        $mode = $config['mode'] ?? Listener::DEFAULT_MODE;
        $listener = new Listener($host, $port, $type, $mode);

        $server = new static($listener->host, $listener->port, $listener->mode, $listener->type);
        echo "Server listen on {$listener->host}:{$listener->port}.\n";

        if (isset($config)) {
            if (isset($config['setting'])) {
                $server->set($config['setting']);
                unset($config['setting']);
            }
            Kawaii::configure($server, $config);
        }

        return $server;
    }

    /**
     * @param int $fd
     * @return bool|Connection
     */
    public function getConnection(int $fd)
    {
        $info = $this->connection_info($fd);
        if (is_array($info)) {
            return new Connection($fd, $info);
        }

        return false;
    }

    /**
     * @param string $name
     * @param null $defaultValue
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
     * @return null|object
     */
    public function createHook(string $className)
    {
        $className = ltrim($this->hookNamespace . '\\' . $className, '\\');
        if (!class_exists($className)) {
            return null;
        }
        return Kawaii::createObject($className);
    }
}