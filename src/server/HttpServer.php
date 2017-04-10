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

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends BaseServer
{
    /**
     * @var string|HttpCallback
     */
    protected $callback = HttpCallback::class;

    /**
     * @param ApplicationInterface $app
     * @return $this
     */
    public function run(ApplicationInterface $app)
    {
        if ($app instanceof HttpHandleInterface) {
            $this->callback->handle = $app;
        }

        if ($app instanceof ApplicationInterface) {
            $app->prepare();
        }

        return $this;
    }

    /**
     * @param Listener $listener
     * @return SwooleServer|SwooleHttpServer
     */
    protected static function swooleServer(Listener $listener): SwooleServer
    {
        return new SwooleHttpServer($listener->host, $listener->port);
    }
}