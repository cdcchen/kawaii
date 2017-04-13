<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/30
 * Time: 13:20
 */

namespace kawaii\server;


use kawaii\base\ApplicationInterface;
use Swoole\Server;

/**
 * Class SocketServer
 * @package kawaii\server
 */
class SocketServer extends BaseServer
{
    /**
     * @var string|SocketCallback
     */
    protected $callback = SocketCallback::class;

    /**
     * @param ApplicationInterface $app
     * @return $this
     */
    public function run(ApplicationInterface $app)
    {
        if ($app instanceof SocketHandleInterface) {
            $this->callback->handle = $app;
        }

        if ($app instanceof ApplicationInterface) {
            $app->prepare();
        }

        return $this;
    }

    /**
     * @param Listener $listener
     * @return Server
     */
    protected static function swooleServer(Listener $listener): Server
    {
        return new Server($listener->host, $listener->port, $listener->type, $listener->mode);
    }
}