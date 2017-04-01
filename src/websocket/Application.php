<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:27
 */

namespace kawaii\websocket;


use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class Application
 * @package kawaii\websocket
 */
class Application extends \kawaii\web\Application implements ApplicationInterface
{
    /**
     * @inheritdoc
     */
    public function handleOpen(Server $server, Request $request)
    {
        echo "App - handleOpen - Websocket {$request->fd} client connected.\n";
    }

    /**
     * @inheritdoc
     */
    public function handleMessage(Server $server, Frame $frame)
    {
        echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";
    }

    /**
     * @inheritdoc
     */
    public function handleClose(Server $server, int $fd, int $reactorId)
    {
        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}