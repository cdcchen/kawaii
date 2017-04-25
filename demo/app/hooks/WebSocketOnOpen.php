<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/25
 * Time: 20:43
 */

namespace app\hooks;


use cdcchen\psr7\ServerRequest;
use kawaii\redis\Connection;
use kawaii\server\BaseHook;
use kawaii\server\ServerTrait;
use Swoole\Server;

class WebSocketOnOpen extends BaseHook
{
    /**
     * @param Server|ServerTrait $server
     * @param int $fd
     * @param ServerRequest $request
     */
    public function run(Server $server, int $fd, ServerRequest $request)
    {
        /* @var Connection $redis */
        $redis = $server->app->getComponent('redis');
        $result = $redis->set('client_config_' . $fd, 'exam');

        var_dump($result);
    }

}