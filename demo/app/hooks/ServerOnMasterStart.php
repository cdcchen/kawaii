<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/24
 * Time: 15:13
 */

namespace app\hooks;


use kawaii\redis\Connection;
use kawaii\server\ServerHookInterface;
use kawaii\server\ServerTrait;
use Swoole\Redis;
use Swoole\Server;

class ServerOnMasterStart implements ServerHookInterface
{
    /**
     * @param Server|ServerTrait $server
     */
    public function run(Server $server)
    {
        $redis = new Redis();
        $redis->on('Message', function (Redis $redis, $result) use ($server) {
            $text = json_decode($result[2], true);
            $message = $text['message'] ?? '';
            $project = $text['project'] ?? null;
            unset($text['message']);
            $text = print_r($text, true) . print_r($message, true);

            /* @var Connection $redis */
//            $redis = $server->app->getComponent('redis');
            foreach ($server->connections as $fd) {
                $connection = $server->getConnection($fd);
                if ($connection->isWebSocket() && $project == 'exam') {
                    $server->push($fd, $text);
                }
            }
        });

        $host = $server->app->params['redis_host'];
        $password = $server->app->params['redis_password'];
        $redis->connect($host, 6379, function (Redis $redis, $result) use ($password) {
            echo $result ? "Redis connect successfully.\n" : "Redis connect failed.\n";
            $redis->auth($password, function (Redis $redis, $result) {
                echo $result ? "Redis auth successfully.\n" : "Redis auth failed.\n";
                $redis->subscribe('prod_log_monitor');
            });
        });
    }
}