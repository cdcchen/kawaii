<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:24
 */

namespace kawaii\server;


use Swoole\Server;

class SocketCallback extends BaseCallback
{
    /**
     * @var SocketHandleInterface
     */
    public $handle;

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onConnect(Server $server, int $fd, int $reactorId): void
    {
        echo "Client {$fd} form reactor {$reactorId} connected.\n";
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        echo "Client {$fd} from reactor {$reactorId} disconnected.\n";
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $fromWorkerId
     * @param string $data
     */
    public function onReceive(Server $server, int $fd, int $fromWorkerId, string $data): void
    {
        echo "Receive data: {$data} from client {$fd}, worker {$fromWorkerId}.\n";
    }

    /**
     * @param Server $server
     * @param string $data
     * @param array $client
     */
    public function onPacket(Server $server, string $data, array $client): void
    {
        $fd = unpack('L', pack('N', ip2long($client['address'])))[1];
        $fromId = ($client['server_socket'] << 16) + $client['port'];
        $server->send($fd, "I had received data: {$data}", $fromId);

        echo "Receive UDP data: {$data} from {$fd}.\n";
    }

    /**
     * @inheritdoc
     */
    public function bind(): void
    {
        parent::bind();

        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Packet', [$this, 'onPacket']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Close', [$this, 'onClose']);
    }
}