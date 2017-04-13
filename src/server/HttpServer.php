<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 15:42
 */

namespace kawaii\server;


use Swoole\Http\Server;

class HttpServer extends Server
{
    use ServerTrait;

    public function run(HttpHandleInterface $app)
    {
        $callback = new HttpCallback();
        $callback->setRequestHandle($app)->bind($this);

        return $this;
    }
}