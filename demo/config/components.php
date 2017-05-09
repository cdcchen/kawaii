<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/7
 * Time: 23:12
 */

return [
    'redis' => [
        'class' => 'kawaii\\redis\\Connection',
        'host' => KAWAII_ENV_PROD ? '2dbf2b5dbcc6435f.redis.rds.aliyuncs.com' : '127.0.0.1',
        'password' => KAWAII_ENV_PROD ? 'yDb123321' : null,
    ],
];