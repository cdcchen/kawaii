<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 18:02
 */

namespace kawaii\server;


trait ServerTrait
{
    /**
     * @param null|string $configFile
     * @return static
     */
    public static function create(?string $configFile)
    {
        if ($configFile === null) {
            $listener = Listener::default();
        } else {
            $config = require($configFile);
            $host = $config['host'] ?? Listener::DEFAULT_HOST;
            $port = $config['port'] ?? Listener::DEFAULT_PORT;
            $type = $config['type'] ?? Listener::DEFAULT_TYPE;
            $mode = $config['mode'] ?? Listener::DEFAULT_MODE;
            $listener = new Listener($host, $port, $type, $mode);
        }

        $server = new static($listener->host, $listener->port, $listener->mode, $listener->type);
        echo "Server listen on {$listener->host}:{$listener->port}.\n";

        if (isset($config)) {
            if (isset($config['setting'])) {
                $server->set($config['setting']);
                unset($config['setting']);
            }
            \Kawaii::configure($server, $config);
        }

        return $server;
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
}