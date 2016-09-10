<?php

//ini_set('default_socket_timeout', -1);

$redis = new Redis();
$result = $redis->connect('192.168.11.22', 6379);
$redis->setOption(Redis::OPT_READ_TIMEOUT, -1);

function fn($redis, $channel, $msg)
{
	var_dump($msg);
}

$redis->subscribe(['monitor'], 'fn');
