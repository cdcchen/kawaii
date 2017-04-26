<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 10:23
 */

namespace app\controllers;

use kawaii\http\Controller;
use kawaii\redis\Connection;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        $wsHost = $this->context->app->params['ws_host'];
        return $this->render('index', [
            'wsHost' => $wsHost,
        ]);
    }

    public function actionHome()
    {
        $conn = $this->context->connection;
        $text = $conn->getParam('username');
        $text .= microtime(true);

        $this->context->connection->setParam('username', $text);

        return 'This is site/home page - ' . $text;
    }

    public function actionRedis()
    {
        /* @var Connection $redis */
        $redis = $this->context->app->getComponent('redis');
        $redis->open();
        $redis->publish('prod_log_monitor', json_encode(['message' => microtime(true) . __FILE__]));
    }
}