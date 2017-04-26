<?php

namespace kawaii\redis;

use kawaii\base\Component;
use Redis;

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/24
 * Time: 16:59
 *
 * Connection
 *
 * @method bool auth(string $password)
 * @method bool select(int $index)
 * @method bool close()
 * @method bool setOption($name, $value)
 * @method bool getOption($name)
 * @method string ping()
 * @method string echo (string $message)
 *
 * Server
 *
 * @method bool bgRewriteAOF()
 * @method bool bgSave()
 * @method bool|array config(string $operation, $value)
 * @method int dbSize()
 * @method bool flushAll()
 * @method bool flushDb()
 * @method array info(string $option = null)
 * @method int lastSave()
 * @method bool resetStat()
 * @method bool save()
 * @method bool slaveOf(string $host = null, int $port = null) no parameter to stop being a slave.
 * @method array time()
 * @method array|int|bool slowLog(string $operation, $length = null)
 *
 * Strings
 *
 * @method bool|mixed get(string $key) If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned.
 * @method bool set(string $key, $value, ...$options)
 * @method bool setEx(string $key, int $ttl, $value) TTL in seconds
 * @method bool pSetEx(string $key, int $ttl, $value) TTL in milliseconds
 * @method bool setNx(string $key, $value)
 * @method bool del(array ...$keys)
 * @method bool delete(array ...$keys) alias of del
 * @method bool exists(string $key)
 * @method int incr(string $key)
 * @method int incrBy(string $key, int $value)
 * @method float incrByFloat(string $key, float $value)
 * @method int decr(string $key)
 * @method int decrBy(string $key, $value)
 * @method array mGet(array $keys)
 * @method string getSet(string $key)
 * @method string randomKey()
 * @method bool move(string $key, int $dbIndex)
 * @method bool rename(string $srcKey, string $dstKey)
 * @method bool renameNx(string $srcKey, string $dstKey)
 * @method bool expire(string $key, int $ttl) TTL in seconds
 * @method bool pexpire(string $key, int $ttl) TTL in milliseconds
 * @method bool expireAt(string $key, int $timestamp) Timestamp in seconds
 * @method bool pexpireAt(string $key, int $timestamp) Timestamp in milliseconds
 * @method array keys(string $pattern)
 * @method array|bool scan(int $iterator, string $pattern, int $count)
 * @method string|bool|int object(string $info, string $key)
 * @method int type(string $key)
 * @method int append(string $key, $value) Size of the value after the append
 * @method string getRange(string $key, int $start, int $end)
 * @method int setRange(string $key, int $offset, $value) the length of the string after it was modified.
 * @method int strLen(string $key)
 * @method int getBit(string $key, int $offset) 0 or 1
 * @method int setBit(string $key, int $offset, int $value) 0 or 1
 * @method int bitOp(string $operation, ...$keys)
 * @method int bitCount(string $key)
 * @method array|int sort(string $key, array $options)
 * @method int ttl(string $key) The time to live in seconds. If the key has no ttl, -1 will be returned, and -2 if the key doesn't exist.
 * @method int pttl(string $key) The time to live in milliseconds. If the key has no ttl, -1 will be returned, and -2 if the key doesn't exist.
 * @method bool persist(string $key)
 * @method bool mSet(array $pairs)
 * @method bool mSetNx(array $pairs)
 * @method string|bool dump(string $key) The Redis encoded value of the key, or FALSE if the key doesn't exist
 * @method bool restore(string $key, int $ttl, string $value)
 * @method bool migrate(string $host, int $port, string | array $key, int $dstDbIndex, int $timeout, bool $copy, bool $replace)
 *
 * Hashes
 *
 * @method int|bool hSet(string $key, string $hashKey, $value) 1 if value didn't exist and was added successfully, 0 if the value was already present and was replaced, FALSE if there was an error.
 * @method int|bool hSetNx(string $key, string $hashKey, $value)
 * @method string hGet(string $key, string $hashKey)
 * @method int hLen(string $key)
 * @method bool hDel(string $key, ...$hashKeys)
 * @method array hKeys(string $key)
 * @method array hVals(string $key)
 * @method array hGetAll(string $key)
 * @method bool hExists(string $key, string $memberKey)
 * @method int hIncrBy(string $key, string $member, int $value)
 * @method float hIncrByFloat(string $key, string $member, float $value)
 * @method bool hMSet(string $key, array $members)
 * @method array hMGet(string $key, array $memberKeys)
 * @method array|bool hScan(string $key, int $iterator, string $pattern, int $count)
 * @method int hStrLen(string $key, string $field) the string length of the value associated with field, or zero when field is not present in the hash or key does not exist at all.
 *
 * Lists
 *
 * Sets
 *
 * Sorted sets
 *
 * Pub/sub
 *
 * @method mixed rawCommand(string $command, ...$params)
 *
 * Scripting
 *
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
    public $readTimeout = -1;
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
            'x' // @todo not complete
        );

        if ($result && $this->password !== null) {
            $result = $this->_redis->auth($this->password);
        }
        if ($result) {
            foreach ($this->options as $name => $value) {
                $this->_redis->setOption($name, $value);
            }
            if ($this->serializer !== null) {
                $this->_redis->setOption(Redis::OPT_SERIALIZER, $this->serializer);
            }
            if ($this->prefix !== null) {
                $this->_redis->setOption(Redis::OPT_PREFIX, $this->prefix);
            }
            $this->_redis->setOption(Redis::OPT_READ_TIMEOUT, $this->readTimeout);

            $this->_redis->select($this->database);
        } else {
            throw new \RedisException('Redis connect or auth failed.');
        }
    }

    /**
     * @inheritdoc
     */
    public function __call($name, $params)
    {
        $this->open();
        if (method_exists($this->_redis, $name)) {
            return call_user_func([$this->_redis, $name], ...$params);
        } else {
            return parent::__call($name, $params);
        }
    }
}