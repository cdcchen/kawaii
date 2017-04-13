<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:26
 */

namespace kawaii\server;


use cdcchen\psr7\ServerRequest;
use kawaii\websocket\Message;
use Swoole\Http\Request;
use Swoole\Server;
use Swoole\WebSocket\Frame;

/**
 * Class WebSocketCallback
 * @package kawaii\server
 */
class WebSocketCallback extends HttpCallback
{
    /**
     * @var WebSocketHandleInterface
     */
    public $messageHandle;

    /**
     * @var ServerRequest[]
     */
    private $requests = [];
    /**
     * @var string[]
     */
    private $_data = [];


    public function setMessageHandle(WebSocketHandleInterface $handle)
    {
        $this->messageHandle = $handle;
        return $this;
    }

    /**
     * @param Server|WebSocketServer $server
     */
    public function bind(Server $server): void
    {
        parent::bind($server);

        $server->on('Open', [$this, 'onOpen']);
        $server->on('Message', [$this, 'onMessage']);
        $server->on('Close', [$this, 'onClose']);
    }

    /**
     * @param bool $flag
     * @return WebSocketCallback
     */
    public function http($flag = true): self
    {
        $this->enableHttp = (bool)$flag;
        return $this;
    }

    /**
     * @param WebSocketServer $server
     * @param Request $req
     */
    public function onOpen(WebSocketServer $server, Request $req): void
    {
        $this->_data[$req->fd] = '';

        $request = static::buildServerRequest($req);
        $this->requests[$req->fd] = $request;

        $this->messageHandle->handleOpen($request, $server);

        echo "App - handleOpen - Websocket {$req->fd} client connected.\n";
        echo var_export($request->getServerParams()) . PHP_EOL;
    }

    /**
     * @param WebSocketServer $server
     * @param Frame $frame
     */
    public function onMessage(WebSocketServer $server, Frame $frame): void
    {
        $fd = $frame->fd;
        $this->_data[$fd] .= $frame->data;
        if ($frame->finish) {
            $request = $this->requests[$fd];
            $connection = new Connection($fd, $server->connection_info($fd));
            $message = new Message($connection, $request, $this->_data[$fd], $frame->opcode);
            $this->messageHandle->handleMessage($message, $server);

            echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";

            unset($this->_data[$fd]);
        } else {
            echo "App - handleMessage - Receive frame data: {$frame->data} form {$frame->fd}.\n";
        }
    }

    /**
     * @param WebSocketServer $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(WebSocketServer $server, int $fd, int $reactorId): void
    {
        unset($this->requests[$fd], $this->_data[$fd]);

        $this->messageHandle->handleClose($server, $fd);

        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}