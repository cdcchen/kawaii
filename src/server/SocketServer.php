<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use Kawaii;
use Swoole\Server;

/**
 * Class SocketServer
 * @package kawaii\base
 */
class SocketServer extends Server
{
    use SwooleServerTrait;
}