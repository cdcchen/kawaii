<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 10:23
 */

namespace app\controllers;

use kawaii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
//        throw new \Exception(__METHOD__);
        $response = \kawaii\Kawaii::createObject('kawaii\web\Response');
        $hash = spl_object_hash($response);
        return __METHOD__ . __CLASS__ . "\n" . self::class . "\n\n" . $hash;
    }

    public function actionTest()
    {
        return __METHOD__;
    }

    public function actionUser($uid)
    {
        return __METHOD__ . ', ' . $uid;
    }

    public function actionEvent()
    {
        return __METHOD__;
    }

    public function actionAaa()
    {
        return __METHOD__;
    }

    public function actionInfo()
    {
        return __METHOD__;
    }
}