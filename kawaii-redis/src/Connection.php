<?php

namespace kawaii\redis;

use kawaii\base\Component;
use Redis;

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/24
 * Time: 16:59
 */
class Connection extends Component
{
    /**
     * @var string the hostname or ip address to use for connecting to the redis server. Defaults to 'localhost'.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $host = 'localhost';
    /**
     * @var integer the port to use for connecting to the redis server. Default port is 6379.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $port = 6379;
    /**
     * @var string the unix socket path (e.g. `/var/run/redis/redis.sock`) to use for connecting to the redis server.
     * This can be used instead of [[hostname]] and [[port]] to connect to the server using a unix socket.
     * If a unix socket path is specified, [[hostname]] and [[port]] will be ignored.
     * @since 2.0.1
     */
    public $unixSocket;
    /**
     * @var string the password for establishing DB connection. Defaults to null meaning no AUTH command is sent.
     * See http://redis.io/commands/auth
     */
    public $password;
    /**
     * @var integer the redis database to use. This is an integer value starting from 0. Defaults to 0.
     * Since version 2.0.6 you can disable the SELECT command sent after connection by setting this property to `null`.
     */
    public $database = 0;
    /**
     * @var float timeout to use for connection to redis.
     */
    public $connectionTimeout = 0;
    /**
     * @var float read timeout
     */
    public $readTimeout = 0;
    /**
     * @var int
     */
    public $retryInterval = 0;
    /**
     * @var int
     */
    public $serializer;
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var array
     */
    public $options = [];

    /**
     * @var Redis
     */
    private $_redis = false;

    public function init(): void
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('Redis extension is not loaded.');
        }
    }

    public function open(): void
    {
        if ($this->_redis !== false) {
            return;
        }

        $this->_redis = new Redis();
        $result = $this->_redis->pconnect(
            $this->unixSocket ?: $this->host,
            $this->port,
            $this->connectionTimeout,
            $this->retryInterval,
            $this->readTimeout
        );

        if ($result && $this->password !== null) {
            $result = $this->_redis->auth($this->password);
        }
        if ($result) {
            $this->_redis->select($this->database);

            foreach ($this->options as $name => $value) {
                $this->_redis->setOption($name, $value);
            }
            if ($this->serializer !== null) {
                $this->_redis->setOption(Redis::OPT_SERIALIZER, $this->serializer);
            }
            if ($this->prefix !== null) {
                $this->_redis->setOption(Redis::OPT_PREFIX, $this->prefix);
            }
        } else {
            echo "Redis connect or auth failed.\n";
        }
    }

    public function close(): void
    {
        $this->_redis->close();
    }

    /**
     * @inheritdoc
     */
    public function __call($name, $params)
    {
        if (method_exists($this->_redis, $name)) {
            $this->open();
            return call_user_func([$this->_redis, $name], ...$params);
        } else {
            return parent::__call($name, $params);
        }
    }
}