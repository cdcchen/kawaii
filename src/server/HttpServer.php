<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:43
 */

namespace kawaii\server;


use Kawaii;

/**
 * Class HttpServer
 * @package kawaii\base
 */
class HttpServer extends \Swoole\Http\Server
{
    use SwooleServerTrait;
    use SwooleHttpServerTrait;

    /**
     * @inheritdoc
     */
    protected function setCallback(): void
    {
        $this->bindHttpCallback();
    }
}