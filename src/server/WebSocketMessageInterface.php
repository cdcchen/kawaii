<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 01:00
 */

namespace kawaii\server;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface WebSocketMessageInterface
 * @package kawaii\server
 */
interface WebSocketMessageInterface
{
    /**
     * @return int
     */
    public function getFd(): int;

    /**
     * @return string
     */
    public function getData(): string;

    /**
     * @return int
     */
    public function getOpcode(): int;

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface;
}