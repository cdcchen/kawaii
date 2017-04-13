<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use kawaii\base\ApplicationInterface;
use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Server;

/**
 * Class WebSocketServer
 * @package kawaii\server
 */
class WebSocketServer extends BaseServer
{
    /**
     * @var string|WebSocketCallback|HttpCallback
     */
    public $callback = WebSocketCallback::class;

    public function run(ApplicationInterface $app, ?ApplicationInterface $app2 = null): self
    {
        if ($app instanceof ApplicationInterface) {
            $app->prepare();
        }
        if ($app instanceof WebSocketHandleInterface) {
            $this->callback->handle1 = $app;
        }
        if ($app2 && ($app2 instanceof HttpHandleInterface)) {
            $this->callback->handle = $app2;
            $this->callback->http(1);
        }

        return $this;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer
     */
    protected static function swooleServer(Listener $listener): SwooleServer
    {
        return new Server($listener->host, $listener->port);
    }
}