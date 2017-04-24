<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/24
 * Time: 16:02
 */

namespace kawaii\server;


use kawaii\base\Object;
use Swoole\Server;

abstract class BaseHook extends Object
{
    /**
     * @param Server|ServerTrait $server
     */
    abstract public function run(Server $server);
}