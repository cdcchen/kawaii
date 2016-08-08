<?php

return [
    'host' => '*',
    'port' => 9502,
    'mode' => 3,
    'type' => 1,
    'daemonize' => 0,

    'worker_num' => 4,
    'max_conn' => 800,
//    'task_worker_num' => 2,

    'log_file' => dirname(__DIR__) . '/log/server.log',
    'log_level' => 1,

    'open_tcp_nodelay' => true,
    'buffer_output_size' => 10240000,

    'discard_timeout_request' => true,
    'enable_reuse_port' => true,

//    'open_http2_protocol' => true,
//    'ssl_cert_file' => '',
//    'ssl_key_file' => '',

    ###########################

    'post_max_size' => 10240000,
];