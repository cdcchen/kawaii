<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/22
 * Time: 11:17
 */

namespace kawaii\server;


/**
 * Class ServerListener
 * @package kawaii\base
 */
use kawaii\base\Object;

/**
 * Class ServerListener
 * @package kawaii\base
 */
class Listener extends Object
{
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 9527;
    const DEFAULT_TYPE = SWOOLE_SOCK_TCP;
    const DEFAULT_MODE = SWOOLE_PROCESS;

    /**
     * @var string
     */
    public $host;
    /**
     * @var int
     */
    public $port;
    /**
     * @var int
     */
    public $type = SWOOLE_SOCK_TCP;
    /**
     * @var int
     */
    public $mode = SWOOLE_PROCESS;

    /**
     * ServerListener constructor.
     * @param string $host
     * @param int $port
     * @param int $type
     * @param int $mode
     * @param array $config
     */
    public function __construct(
        string $host = self::DEFAULT_HOST,
        int $port = self::DEFAULT_PORT,
        int $type = self::DEFAULT_TYPE,
        int $mode = self::DEFAULT_MODE,
        array $config = []
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->type = $type;
        $this->mode = $mode;

        parent::__construct($config);
    }

    /**
     * @return static|self
     */
    public static function default(): self
    {
        return new static();
    }
}