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
        'host' => KAWAII_ENV_PROD ? 'define(\'KAWAII_ENV\', getenv(\'KAWAII_ENV\'));' : '127.0.0.1',
        'password' => KAWAII_ENV_PROD ? 'yDb123321' : null,
    ],
];