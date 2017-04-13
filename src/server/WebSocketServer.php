<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 15:43
 */

namespace kawaii\server;


use kawaii\base\ApplicationInterface;
use Swoole\WebSocket\Server;

class WebSocketServer extends Server
{
    public function run(ApplicationInterface $app, $http = false)
    {
        if ($app instanceof WebSocketHandleInterface) {
            $callback = new WebSocketCallback();
            $callback->setMessageHandle($app)->bind($this);
        }

        if ($http && $app instanceof HttpHandleInterface) {
            $callback = new HttpCallback();
            $callback->setRequestHandle($app)->bind($this);
        }

        return $this;
    }
}