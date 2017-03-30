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
    /**
     * @var string
     */
    public $host = '0.0.0.0';
    /**
     * @var int
     */
    public $port = 9527;
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
        string $host,
        int $port,
        int $type = SWOOLE_SOCK_TCP,
        int $mode = SWOOLE_PROCESS,
        array $config = []
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->type = $type;
        $this->mode = $mode;

        parent::__construct($config);
    }
}