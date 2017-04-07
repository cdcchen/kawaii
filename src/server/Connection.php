<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/7
 * Time: 20:09
 */

namespace kawaii\server;


use kawaii\base\Object;

/**
 * Class Connection
 * @package kawaii\server
 */
class Connection extends Object
{
    /**
     * @var int
     */
    public $webSocketStatus = 0;
    /**
     * @var int
     */
    public $serverPort;
    /**
     * @var int
     */
    public $serverFd;
    /**
     * @var int
     */
    public $socketType;
    /**
     * @var int
     */
    public $remotePort;
    /**
     * @var string
     */
    public $remoteIp;
    /**
     * @var int
     */
    public $fromId;
    /**
     * @var int
     */
    public $connectTime;
    /**
     * @var int
     */
    public $lastTime;
    /**
     * @var int
     */
    public $closeErrno = 0;

    /**
     * Connection constructor.
     * @param array $data
     * @param array $config
     */
    public function __construct($data, array $config = [])
    {
        parent::__construct($config);

        $this->webSocketStatus = $data['websocket_status'] ?? 0;
        $this->serverPort = $data['server_port'] ?? 0;
        $this->serverFd = $data['server_ip'] ?? 0;
        $this->socketType = $data['socket_type'] ?? 0;
        $this->remotePort = $data['remote_type'] ?? 0;
        $this->remoteIp = $data['remote_ip'] ?? 0;
        $this->fromId = $data['from_id'] ?? 0;
        $this->connectTime = $data['connect_time'] ?? 0;
        $this->lastTime = $data['last_time'] ?? 0;
        $this->closeErrno = $data['close_errno'] ?? 0;
    }

    /**
     * @return bool
     */
    public function isWebSocket()
    {
        return $this->webSocketStatus === WEBSOCKET_STATUS_ACTIVE;
    }

}