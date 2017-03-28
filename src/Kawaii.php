<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 13:55
 */

use kawaii\di\Container;

/**
 * Class Kawaii
 */
class Kawaii extends \kawaii\BaseKawaii
{
}

spl_autoload_register([Kawaii::class, 'autoload'], true, true);
Kawaii::$classMap = require(__DIR__ . '/classes.php');
Kawaii::$container = new Container();
