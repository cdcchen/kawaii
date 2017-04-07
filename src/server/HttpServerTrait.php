<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 19:40
 */

namespace kawaii\server;


use Closure;
use Kawaii;

/**
 * Class HttpServerTrait
 * @package kawaii\server
 */
trait HttpServerTrait
{
    /**
     * @var callable|SwooleHttpHandle
     */
    private $requestCallback;
    /**
     * @var callable|HttpHandleInterface
     */
    private $requestHandle;

    /**
     * @param callable|HttpHandleInterface $callback
     */
    public function onRequest(callable $callback): void
    {
        $this->requestHandle = $callback instanceof Closure ? $callback->bindTo($this) : $callback;
    }

    /**
     * Set http onRequest callback
     */
    protected function setHttpCallback(): void
    {
        $this->receiveCallback = $this->connectCallback = null;
        $this->requestCallback = new SwooleHttpHandle($this, $this->requestHandle);
    }
}