<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/7
 * Time: 20:09
 */

namespace mars;


/**
 * Class Connection
 * @package mars
 */
class Connection
{
    /**
     * @var int
     */
    private $fd = 0;
    /**
     * @var int
     */
    private $webSocketStatus = 0;
    /**
     * @var int
     */
    private $serverPort;
    /**
     * @var int
     */
    private $serverFd;
    /**
     * @var int
     */
    private $socketType;
    /**
     * @var int
     */
    private $remotePort;
    /**
     * @var string
     */
    private $remoteIP;
    /**
     * @var int
     */
    private $fromId;
    /**
     * @var int
     */
    private $connectTime;
    /**
     * @var int
     */
    private $lastTime;
    /**
     * @var int
     */
    private $closeErrno = 0;

    /**
     * Connection constructor.
     * @param int $fd
     * @param array $data
     */
    public function __construct(int $fd, array $data)
    {
        $this->fd = $fd;
    }

    /**
     * @return bool
     */
    public function isWebSocket(): bool
    {
        return $this->webSocketStatus === WEBSOCKET_STATUS_ACTIVE;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getWebSocketStatus(): int
    {
        if ($this->webSocketStatus === null) {
            $this->webSocketStatus = $data['websocket_status'] ?? 0;
        }

        return $this->webSocketStatus;
    }

    /**
     * @return int
     */
    public function getServerPort(): int
    {
        if ($this->serverPort === null) {
            $this->serverPort = $data['server_port'] ?? 0;
        }

        return $this->serverPort;
    }

    /**
     * @return int
     */
    public function getServerFd(): int
    {
        if ($this->serverFd === null) {
            $this->serverFd = $data['server_fd'] ?? 0;
        }

        return $this->serverFd;
    }

    /**
     * @return int
     */
    public function getSocketType(): int
    {
        if ($this->socketType === null) {
            $this->socketType = $data['socket_type'] ?? 0;
        }

        return $this->socketType;
    }

    /**
     * @return int
     */
    public function getRemotePort(): int
    {
        if ($this->remotePort === null) {
            $this->remotePort = $data['remote_type'] ?? 0;
        }

        return $this->remotePort;
    }

    /**
     * @return string
     */
    public function getRemoteIP(): string
    {
        if ($this->remoteIP === null) {
            $this->remoteIP = $data['remote_ip'] ?? '';
        }

        return $this->remoteIP;
    }

    /**
     * @return int
     */
    public function getFromId(): int
    {
        if ($this->fromId === null) {
            $this->fromId = $data['from_id'] ?? 0;
        }

        return $this->fromId;
    }

    /**
     * @return int
     */
    public function getConnectTime(): int
    {
        if ($this->connectTime === null) {
            $this->connectTime = $data['connect_time'] ?? 0;
        }

        return $this->connectTime;
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        if ($this->lastTime === null) {
            $this->lastTime = $data['last_time'] ?? 0;
        }

        return $this->lastTime;
    }

    /**
     * @return int
     */
    public function getCloseErrno(): int
    {
        if ($this->closeErrno === null) {
            $this->closeErrno = $data['close_errno'] ?? 0;
        }

        return $this->closeErrno;
    }
}