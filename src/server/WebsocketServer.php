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
    protected $openHandle;
    protected $messageHandle;
    protected $handShakeHandle;

    public function http(): self
    {
        $this->setRequestHandle();
        return $this;
    }

    public function onOpen(callable $callback): void
    {
        $this->openHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    public function onMessage(callable $callback): void
    {
        $this->messageHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    public function onHandShake(callable $callback): void
    {
        $this->handShakeHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
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
        if (is_callable($this->requestHandle)) {
            $this->swoole->on('Request', $this->requestHandle);
        } else {
            throw new UnexpectedValueException('onRequest callback is not callable.');
        }

        if (is_callable($this->openHandle)) {
            $this->swoole->on('Open', $this->openHandle);
        } else {
            throw new UnexpectedValueException('openHandle callback is not callable.');
        }

        if (is_callable($this->messageHandle)) {
            $this->swoole->on('Message', $this->messageHandle);
        } else {
            throw new UnexpectedValueException('messageHandle callback is not callable.');
        }

        if (is_callable($this->handShakeHandle)) {
            $this->swoole->on('HandShake', $this->handShakeHandle);
        } elseif ($this->handShakeHandle !== null) {
            throw new UnexpectedValueException('Handle callback is not callable.');
        }

        parent::bindCallback();
    }

    protected function setCallback(): void
    {
        $this->receiveHandle = $this->connectHandle = null;

        $this->openHandle = function (Server $server, SwooleHttpRequest $request): void {
            echo "Websocket {$request->fd} client connected.\n";
        };

        $this->messageHandle = function (SwooleServer $server, Frame $frame): void {
            echo "Receive message: {$frame->data} form {$frame->fd}.\n";
        };
    }
}