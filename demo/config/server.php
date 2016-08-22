<?php

return [
    'pid_file' => __DIR__ . '/../tmp/kawaii.pid',

    'post_max_size' => 10240000,

    'swoole' => require(__DIR__ . '/swoole.php'),
];