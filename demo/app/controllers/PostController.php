<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/7
 * Time: 11:20
 */

namespace app\controllers;


use kawaii\http\Controller;

class PostController extends Controller
{
    public function actionIndex()
    {
        $this->context->connection->setParam('username', 'cdcchen');
        return __METHOD__ . microtime(true);
    }

    public function actionProfile()
    {
        return __METHOD__;
    }
}