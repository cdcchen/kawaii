<?php

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/4
 * Time: 19:54
 */

namespace app\controllers\user;


use Kawaii;
use kawaii\web\Controller;

class HomeController extends Controller
{
    public function actionIndex($id)
    {
        return __METHOD__ . var_export($id, true) . var_export($this->actionParams, true)
        . '<br />' . PHP_EOL
        . var_export($this->getRequest()->getQueryParams(), 1)
        . var_export(Kawaii::$app->params, 1)
        . Kawaii::$app->getBasePath()
        . Kawaii::$app->id
        . Kawaii::$app->name;
    }

    public function actionProfile()
    {
        return __METHOD__;
    }
}