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
use kawaii\base\InvalidConfigException;
use kawaii\websocket\Application;
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
     * @param Application $app
     * @return $this
     * @throws InvalidConfigException
     */
    public function run(Application $app)
    {
        $app->prepare();

        if ($app->handle instanceof WebSocketHandleInterface) {
            $this->setWebSocketCallback($app->handle);
        } else {
            echo "Use default websocket handle.\n";
        }

        return $this;
    }

    /**
     * @param callable|HttpHandleInterface $callback
     * @return $this
     */
    public function http(callable $callback)
    {
        if ($callback instanceof ApplicationInterface) {
            $callback->prepare();
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
    protected static function swooleServer(Listener $listener): SwooleServer
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
            $this->getSwoole()->on('Open', $this->openCallback);
        } elseif ($this->openCallback !== null) {
            throw new UnexpectedValueException('openCallback callback is not callable.');
        }

        if (is_callable($this->messageCallback)) {
            $this->getSwoole()->on('Message', $this->messageCallback);
        } else {
            throw new UnexpectedValueException('messageCallback callback is not callable.');
        }

        if (is_callable($this->handShakeCallback)) {
            $this->getSwoole()->on('HandShake', $this->handShakeCallback);
        } elseif ($this->handShakeCallback !== null) {
            throw new UnexpectedValueException('Callback callback is not callable.');
        }

        if (is_callable($this->requestCallback)) {
            $this->getSwoole()->on('Request', $this->requestCallback);

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
        $this->setWebSocketCallback();
    }

    /**
     * @param WebSocketHandleInterface $handle
     */
    private function setWebSocketCallback(?WebSocketHandleInterface $handle = null): void
    {
        $callback = new SwooleWebSocketHandle($this, $handle);
        $this->openCallback = [$callback, 'onOpen'];
        $this->messageCallback = [$callback, 'onMessage'];
        $this->closeCallback = [$callback, 'onClose'];
    }
}