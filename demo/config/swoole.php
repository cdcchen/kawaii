<?php

return [
    'host' => '0.0.0.0',
    'port' => 9527,
    'mode' => SWOOLE_PROCESS,
    'type' => SWOOLE_TCP,
    'daemonize' => 0,

    'reactor_num' => 4,
    //    'worker_num' => 4,
    'max_conn' => 200,
    'task_worker_num' => 2,

    'log_file' => dirname(__DIR__) . '/log/server.log',
    'log_level' => 4,

    'open_tcp_nodelay' => true,
    //    'buffer_output_size' => 10240000,

    'discard_timeout_request' => true,
    'enable_reuse_port' => true,
    'open_eof_check' => true,
    'package_eof' => "\r\n\r\n",

    'open_tcp_keepalive' => true,

    //    'open_http2_protocol' => true,
    //    'ssl_cert_file' => '',
    //    'ssl_key_file' => '',
];
