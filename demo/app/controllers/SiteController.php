<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 10:23
 */

namespace app\controllers;

use kawaii\http\Controller;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', ['hello' => 'Hello world!!']);
    }

    public function actionHome()
    {
        return 'This is site/home page';
    }
}