<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use Kawaii;
use kawaii\base\ApplicationInterface;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;
use UnexpectedValueException;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends BaseServer
{
    use HttpServerTrait;

    /**
     * @param callable|ApplicationInterface|HttpServerRequestHandleInterface $callback
     * @return $this
     */
    public function run(callable $callback)
    {
        if ($callback instanceof ApplicationInterface) {
            $callback->run();
        }
        $this->requestHandle = $callback;
        $this->setHttpCallback();

        return $this;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer|SwooleHttpServer
     */
    protected static function createSwooleServer(Listener $listener): SwooleServer
    {
        return new SwooleHttpServer($listener->host, $listener->port);
    }

    /**
     * @inheritdoc
     */
    protected function bindCallback(): void
    {
        parent::bindCallback();

        if (is_callable($this->requestCallback)) {
            $this->swoole->on('Request', $this->requestCallback);
        } else {
            throw new UnexpectedValueException('requestCallback is not callable.');
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