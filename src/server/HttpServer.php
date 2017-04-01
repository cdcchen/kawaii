<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use Kawaii;
use Swoole\Http\Server as SwooleHttpServer;
use Swoole\Server as SwooleServer;
use UnexpectedValueException;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends Base
{
    use HttpServerTrait;

    /**
     * @param callable $callback
     * @return $this
     */
    public function run(callable $callback)
    {
        $this->requestHandle = $callback;
        $this->setHttpCallback();

        return $this;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer
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
        if (is_callable($this->requestCallback)) {
            $this->swoole->on('Request', $this->requestCallback);
        } else {
            throw new UnexpectedValueException('requestCallback is not callable.');
        }

        parent::bindCallback();
    }

    /**
     * @inheritdoc
     */
    protected function setCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;
    }
}