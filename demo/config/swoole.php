<?php

$appPath = dirname(__DIR__);

return [
    'host' => '0.0.0.0',
    'port' => 9502,
    'mode' => SWOOLE_PROCESS,
    'type' => SWOOLE_TCP,
    'daemonize' => 0,
    'pid_file' => $appPath . '/tmp/kawaii.pid',

    'reactor_num' => 1,
    'worker_num' => 1,
    'max_request' => 1000,
    'task_max_request' => 1000,
    'max_conn' => 200,
    'task_worker_num' => 2,

    'log_file' => $appPath . '/log/server.log',
    'log_level' => 0,

    'backlog' => 128,

//    'heartbeat_check_interval' => 60,
//    'heartbeat_idle_time' => 300,

    'open_tcp_nodelay' => true,
    'buffer_output_size' => 10240000,

    //    'discard_timeout_request' => true,
    'enable_reuse_port' => true,
    //    'open_eof_check' => true,
    //    'package_eof' => "\r\n",

    'tcp_keepidle' => 600,
    'tcp_keepcount' => 5,
    'tcp_keepinterval' => 60,


    //    'open_http2_protocol' => false,
    //    'open_http2_protocol' => true,
    //    'ssl_cert_file' => '',
    //    'ssl_key_file' => '',

    'user' => '',
    'group' => '',
    'chroot' => dirname(__DIR__),


    /**
     * swoole_http_server only
     */
    'upload_tmp_dir' => $appPath . '/tmp/post_temp',
    'http_parse_post' => true,
];
