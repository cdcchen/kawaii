<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 00:24
 */

namespace kawaii\server;


use kawaii\base\Object;
use kawaii\websocket\Message;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class SwooleWebSocketHandle extends Object
{
    /**
     * @var BaseServer
     */
    public $server;

    /**
     * @var WebSocketHandleInterface
     */
    public $handle;

    /**
     * SwooleWebSocketHandle constructor.
     * @param BaseServer $server
     * @param WebSocketHandleInterface $handle
     * @param array $config
     */
    public function __construct(BaseServer $server, ?WebSocketHandleInterface $handle = null, array $config = [])
    {
        parent::__construct($config);
        $this->server = $server;
        $this->handle = $handle;
    }

    /**
     * @param Server $server
     * @param Request $req
     * @return mixed
     */
    public function onOpen(Server $server, Request $req)
    {
        $request = SwooleHttpHandle::buildServerRequest($req);
        if ($this->handle instanceof WebSocketHandleInterface) {
            $this->handle->handleOpen($request, $server);
        }

        echo "App - handleOpen - Websocket {$req->fd} client connected.\n";
        echo var_export($request->getServerParams()) . PHP_EOL;
    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame)
    {
        $message = new Message();
        if ($this->handle instanceof WebSocketHandleInterface) {
            $this->handle->handleMessage($message, $server);
        }
        echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId)
    {
        if ($this->handle instanceof WebSocketHandleInterface) {
            $this->handle->handleClose($server, $fd);
        }
        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}