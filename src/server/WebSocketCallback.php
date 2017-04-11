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
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class WebSocketCallback
 * @package kawaii\server
 */
class WebSocketCallback extends HttpCallback
{
    /**
     * @var WebSocketHandleInterface
     */
    public $handle1;
    /**
     * @var bool
     */
    protected $enableHttp = false;

    /**
     * @var ServerRequest[]
     */
    private $requests = [];
    /**
     * @var string[]
     */
    private $_data = [];

    /**
     * @inheritdoc
     */
    public function bind(): void
    {
        parent::bind();

        $this->server->on('Open', [$this, 'onOpen']);
        $this->server->on('Message', [$this, 'onMessage']);
        $this->server->on('Close', [$this, 'onClose']);
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
     * @param Server $server
     * @param Request $req
     */
    public function onOpen(Server $server, Request $req): void
    {
        $this->_data[$req->fd] = '';

        $request = static::buildServerRequest($req);
        $this->requests[$req->fd] = $request;

        $this->handle1->handleOpen($request, $server);

        echo "App - handleOpen - Websocket {$req->fd} client connected.\n";
        echo var_export($request->getServerParams()) . PHP_EOL;
    }

    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame): void
    {
        $fd = $frame->fd;
        $this->_data[$fd] .= $frame->data;
        if ($frame->finish) {
            $request = $this->requests[$fd];
            $message = new Message($fd, $request, $this->_data[$fd], $frame->opcode);
            $this->handle1->handleMessage($message, $server);

            echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";

            unset($this->_data[$fd]);
        } else {
            echo "App - handleMessage - Receive frame data: {$frame->data} form {$frame->fd}.\n";
        }
    }

    /**
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        unset($this->requests[$fd], $this->_data[$fd]);

        $this->handle1->handleClose($server, $fd);

        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}