<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/7
 * Time: 23:14
 */

return [
    'email' => 'admin@cdc.com',
    'ws_host' => KAWAII_ENV_PROD ? 'ws://log.mschool.cn:9527/log' : 'ws://127.0.0.1:9527/log',
    'redis_host' => KAWAII_ENV_PROD ? '2dbf2b5dbcc6435f.redis.rds.aliyuncs.com' : '127.0.0.1',
    'redis_password' => KAWAII_ENV_PROD ? 'yDb123321' : null,
];