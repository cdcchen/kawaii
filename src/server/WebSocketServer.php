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
use Swoole\WebSocket\{
    Server
};

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

    /**
     * @param ApplicationInterface $app
     * @param bool $enableHttp
     * @return $this|WebSocketServer
     */
    public function run(ApplicationInterface $app, $enableHttp = false): self
    {
        if ($app instanceof ApplicationInterface) {
            $app->prepare();
        }
        if ($app instanceof WebSocketHandleInterface) {
            $this->callback->handle = $app;
        }
        if ($enableHttp && ($app instanceof HttpHandleInterface)) {
            var_dump(__FILE__);
            $this->callback->http($enableHttp);
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