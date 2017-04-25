#!/usr/bin/env php

<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/15
 * Time: 11:30
 */
$redis = new \Swoole\Redis();
$redis->connect('127.0.0.1', 6379, function (\Swoole\Redis $redis, $result) {
    echo $result ? "Connect redis host successfully.\n" : 'Connect redis host failed.\n';
    var_dump($result);

    swoole_timer_tick(1000, function (int $timerId, $params) use ($redis) {
        $message = ['project' => 'exam', 'message' => microtime(true)];
        $redis->publish('prod_log_monitor', json_encode($message, 512), function (\Swoole\Redis $redis, $result) {
            var_export($result);
        });
    }, []);
});
