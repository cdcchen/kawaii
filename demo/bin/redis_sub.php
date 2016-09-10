<?php

$redis = new Redis();
$result = $redis->connect('192.168.11.22', 6379);

function fn($redis, $channel, $msg)
{
	var_dump($msg);
}

$redis->subscribe(['monitor'], 'fn');

