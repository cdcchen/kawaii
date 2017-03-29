<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/24
 * Time: 15:03
 */

namespace kawaii\server;


use kawaii\base\ApplicationInterface;
use kawaii\base\InvalidConfigException;


class WebsocketServer extends Base
{
    /**
     * @var callable
     */
    public $onOpen = [EventHandle::class, 'onOpen'];
    /**
     * @var callable
     */
    public $onMessage = [EventHandle::class, 'onMessage'];
    /**
     * @var callable
     */
    public $onHandShake;

    /**
     * bind swoole server event
     */
    protected function setCallback(): void
    {
        if (is_callable($this->onOpen)) {
            $this->on('Open', $this->onOpen);
        }
        if (is_callable($this->onMessage)) {
            $this->on('Message', $this->onMessage);
        } elseif (is_callable($this->onHandShake)) {
            $this->on('HandShake', $this->onHandShake);
        } else {
            throw new InvalidConfigException('onMessage or onHandShake must be callable.');
        }
    }

    /**
     * @param ApplicationInterface $app
     * @return WebsocketServer
     */
    public function http(ApplicationInterface $app): self
    {
        $this->bindHttpCallback();
        $app->run();

        return $this;
    }
    protected function bindCallback(): void
    {
        // TODO: Implement bindCallback() method.
    }

    static protected function createSwooleServer(Listener $listener): SwooleServer
    {
        // TODO: Implement createSwooleServer() method.
    }
}