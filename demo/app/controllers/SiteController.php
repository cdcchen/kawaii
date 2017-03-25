<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 10:23
 */

namespace app\controllers;

use app\tasks\TestTask;
use kawaii\web\Controller;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
//        print_r(\Kawaii::$aliases);
//        throw new \Exception(__METHOD__);
        $task = new TestTask();
        $task->setData($this->className());
        \Kawaii::$server->asyncTask($task);
        return $this->render('index', ['hello' => 'Hello world!!']);
    }

    public function actionTest()
    {

        $response = \Kawaii::createObject('kawaii\web\Response');
        $hash = spl_object_hash($response);
        return __METHOD__ . "\n\n" . $hash;
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
        echo __FILE__, PHP_EOL;
        echo get_class($this), PHP_EOL;
        echo $this->getHash(), PHP_EOL;
        var_dump($this->equals($this));
        echo PHP_EOL;
        echo $this->getReflectionClass()->getDocComment(), PHP_EOL;

        return __METHOD__;
    }

    public function actionInfo()
    {
        return __METHOD__;
    }
}