<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 13:55
 */

namespace kawaii;


use kawaii\di\Container;

class Kawaii extends BaseKawaii
{
}

spl_autoload_register([Kawaii::className(), 'autoload'], true, true);
Kawaii::$classMap = require(__DIR__ . '/classes.php');
Kawaii::$container = new Container();