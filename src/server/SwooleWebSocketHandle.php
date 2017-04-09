<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 00:24
 */

namespace kawaii\server;


use cdcchen\psr7\ServerRequest;
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
     * @var ServerRequest[]
     */
    private $requests = [];
    /**
     * @var string[]
     */
    private $_data = [];

    /**
     * SwooleWebSocketHandle constructor.
     * @param BaseServer $server
     * @param WebSocketHandleInterface $handle
     * @param array $config
     */
    public function __construct(BaseServer $server, WebSocketHandleInterface $handle, array $config = [])
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
        $this->_data[$req->fd] = '';

        $request = SwooleHttpHandle::buildServerRequest($req);
        $this->requests[$req->fd] = $request;

        $this->handle->handleOpen($request, $server);

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
        $fd = $frame->fd;
        $this->_data[$fd] .= $frame->data;
        if ($frame->finish) {
            $request = $this->requests[$fd];
            $message = new Message($fd, $request, $this->_data[$fd], $frame->opcode);
            $this->handle->handleMessage($message, $server);

            echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";

            $this->_data[$fd] = '';
        } else {
            echo "App - handleMessage - Receive frame data: {$frame->data} form {$frame->fd}.\n";
        }
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId)
    {
        unset($this->requests[$fd], $this->_data[$fd]);

        $this->handle->handleClose($server, $fd);

        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}