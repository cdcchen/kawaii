<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 13:55
 */

use kawaii\di\Container;

class Kawaii extends \kawaii\BaseKawaii
{
}

spl_autoload_register([Kawaii::class, 'autoload'], true, true);
Kawaii::$container = new Container();