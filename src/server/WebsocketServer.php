<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use Closure;
use kawaii\websocket\ApplicationInterface;
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
class WebsocketServer extends Base
{
    use HttpServerTrait;

    /**
     * @var callable
     */
    protected $openCallback;
    /**
     * @var callable
     */
    protected $messageCallback;
    /**
     * @var callable
     */
    protected $handShakeCallback;

    /**
     * @param ApplicationInterface $app
     * @return $this
     */
    public function run(ApplicationInterface $app)
    {
        $this->messageCallback = [$app, 'handleMessage'];

        if (method_exists($app, 'handleOpen')) {
            $this->openCallback = [$app, 'handleOpen'];
        }
        if (method_exists($app, 'handleClose')) {
            $this->closeCallback = [$app, 'handleClose'];
        }

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function http(callable $callback)
    {
        $this->setHttpCallback();
        $this->requestHandle = $callback;
        return $this;
    }

    /**
     * @param callable $callback
     */
    public function onOpen(callable $callback): void
    {
        $this->handShakeCallback = null;
        $this->openCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onMessage(callable $callback): void
    {
        $this->messageCallback = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * @param callable $callback
     */
    public function onHandShake(callable $callback): void
    {
        $this->openCallback = null;
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
        if (is_callable($this->openCallback)) {
            $this->swoole->on('Open', $this->openCallback);
        } elseif ($this->openCallback !== null) {
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

        if (is_callable($this->requestCallback)) {
            $this->swoole->on('Request', $this->requestCallback);

            if (!is_callable($this->requestHandle)) {
                throw new UnexpectedValueException('requestHandle is not callable.');
            }
        }

        parent::bindCallback();
    }

    /**
     * @inheritdoc
     */
    protected function setCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;

        $this->openCallback = function (Server $server, SwooleHttpRequest $request): void {
            echo "Websocket {$request->fd} client connected.\n";
        };

        $this->messageCallback = function (SwooleServer $server, Frame $frame): void {
            echo "Receive message: {$frame->data} form {$frame->fd}.\n";
        };

        $this->onClose(function (SwooleServer $server, int $fd, int $reactorId): void {
            echo "WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
        });
    }
}