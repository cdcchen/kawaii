<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\{
    Frame, Server
};

/**
 * Class WebsocketServer
 * @package kawaii\server
 */
class WebsocketServer extends HttpServer
{

    /**
     *
     */
    protected function bindCallback(): void
    {
        parent::bindCallback();
        static::$swooleServer->on('Open', [$this, 'onOpen']);
        static::$swooleServer->on('Message', [$this, 'onMessage']);
//        static::$swooleServer->on('HandShake', [$this, 'onHandShake']);
    }

    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    static protected function createSwooleServer(Listener $listener): SwooleServer
    {
        return new Server($listener->host, $listener->port);
    }

    /**
     * @param Server $server
     * @param SwooleHttpRequest $request
     */
    public function onOpen(Server $server, SwooleHttpRequest $request): void
    {
        echo "Websocket client connected\n";
    }

    /**
     * @param SwooleServer $server
     * @param Frame $frame
     */
    public function onMessage(SwooleServer $server, Frame $frame): void
    {
        echo "Receive message: {$frame->data}\n";
    }

    /**
     * @param SwooleHttpRequest $request
     * @param SwooleHttpResponse $response
     * @return bool
     */
    public function onHandShake(SwooleHttpRequest $request, SwooleHttpResponse $response): bool
    {

    }
}