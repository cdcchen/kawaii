<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use Closure;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\{
    Frame, Server
};
use UnexpectedValueException;

/**
 * Class WebsocketServer
 * @package kawaii\server
 */
class WebsocketServer extends HttpServer
{
    protected $openCallback;
    protected $messageCallback;
    protected $handShakeCallback;

    public function onOpen(callable $callback): void
    {
        $this->openCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    public function onMessage(callable $callback): void
    {
        $this->messageCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    public function onHandShake(callable $callback): void
    {
        $this->handShakeCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    protected static function createSwooleServer(Listener $listener): SwooleServer
    {
        return new Server($listener->host, $listener->port);
    }

    /**
     * @inheritdoc
     */
    protected function bindCallback(): void
    {
        if (is_callable($this->requestCallback)) {
            $this->swoole->on('Request', $this->requestCallback);
        } else {
            throw new UnexpectedValueException('onRequest callback is not callable.');
        }

        if (is_callable($this->openCallback)) {
            $this->swoole->on('Open', $this->openCallback);
        } else {
            throw new UnexpectedValueException('openCallback callback is not callable.');
        }

        if (is_callable($this->messageCallback)) {
            $this->swoole->on('Message', $this->messageCallback);
        } else {
            throw new UnexpectedValueException('messageCallback callback is not callable.');
        }

        if (is_callable($this->handShakeCallback)) {
            $this->swoole->on('HandShake', $this->handShakeCallback);
        } elseif ($this->handShakeCallback !== null) {
            throw new UnexpectedValueException('Callback callback is not callable.');
        }

        parent::bindCallback();
    }

    protected function setCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;

        $this->openCallback = function (Server $server, SwooleHttpRequest $request): void {
            echo "Websocket {$request->fd} client connected.\n";
        };

        $this->messageCallback = function (SwooleServer $server, Frame $frame): void {
            echo "Receive message: {$frame->data} form {$frame->fd}.\n";
        };
    }
}