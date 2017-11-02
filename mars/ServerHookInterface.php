<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/26
 * Time: 14:16
 */

namespace mars;


use Swoole\Server;

/**
 * Interface ServerHookInterface
 * @package mars
 */
interface ServerHookInterface
{
    /**
     * @param Server|HttpServer $server
     */
    public function run(Server $server);
}