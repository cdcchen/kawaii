<?php

return [
    'host' => '0.0.0.0',
    'port' => 9527,
    'mode' => SWOOLE_PROCESS,
    'type' => SWOOLE_TCP,

    'access_log' => 'x',
    'error_log' => 'y',
    'server_signature' => 'Kawaii-Server',
    'setting' => require(__DIR__ . '/swoole.php'),
];