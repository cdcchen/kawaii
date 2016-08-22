<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/22
 * Time: 11:17
 */

namespace kawaii\base;


/**
 * Class ServerListener
 * @package kawaii\base
 */
/**
 * Class ServerListener
 * @package kawaii\base
 */
class ServerListener
{
    /**
     * @var string
     */
    public $host = '0.0.0.0';
    /**
     * @var int
     */
    public $port = 9502;
    /**
     * @var int
     */
    public $mode = SWOOLE_PROCESS;
    /**
     * @var int
     */
    public $type = SWOOLE_SOCK_TCP;

    /**
     * ServerListener constructor.
     * @param int $port
     * @param string $host
     * @param int $type
     * @param int $mode
     */
    public function __construct($port, $host, $type = SWOOLE_SOCK_TCP, $mode = SWOOLE_PROCESS)
    {
        $this->host = $host;
        $this->port = $port;
        $this->type = $type;
        $this->mode = $mode;
    }
}