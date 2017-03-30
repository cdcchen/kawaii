<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/3/30
 * Time: 11:13
 */

namespace kawaii\server;


use kawaii\base\Object;
use swoole_server_port;

/**
 * Class PortServer
 * @package kawaii\server
 */
class PortServer extends Object
{
    /**
     * @var array
     */
    public $setting = [];
    /**
     * @var callable
     */
    public $connectHandle;
    /**
     * @var callable
     */
    public $closeHandle;
    /**
     * @var callable
     */
    public $receiveHandle;
    /**
     * @var callable
     */
    public $packetHandle;

    /**
     * @param swoole_server_port $port
     */
    public function run(swoole_server_port $port): void
    {
        $port->set($this->setting);

        if (is_callable($this->connectHandle)) {
            $port->on('Connect', $this->connectHandle);
        }
        if (is_callable($this->closeHandle)) {
            $port->on('Close', $this->closeHandle);
        }
        if (is_callable($this->receiveHandle)) {
            $port->on('Receive', $this->receiveHandle);
        }
        if (is_callable($this->packetHandle)) {
            $port->on('Packet', $this->packetHandle);
        }
    }
}