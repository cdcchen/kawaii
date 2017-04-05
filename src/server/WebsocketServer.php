<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use Closure;
use kawaii\base\ApplicationInterface;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\{
    Server
};
use UnexpectedValueException;

/**
 * Class WebsocketServer
 * @package kawaii\server
 */
class WebsocketServer extends BaseServer
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
     * @param WebSocketHandleInterface $app
     * @return $this
     */
    public function run(WebSocketHandleInterface $app)
    {
        if ($app instanceof ApplicationInterface) {
            $app->run();
        }

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
     * @param callable|HttpServerRequestHandleInterface $callback
     * @return $this
     */
    public function http(callable $callback)
    {
        if ($callback instanceof ApplicationInterface) {
            $callback->run();
        }

        $this->requestHandle = $callback;
        $this->setHttpCallback();

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
        parent::bindCallback();

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
    }

    /**
     * @inheritdoc
     */
    protected function setCallback(): void
    {
        parent::setCallback();
        $this->receiveCallback = $this->connectCallback = null;

    }
}