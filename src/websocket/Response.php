<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/11
 * Time: 20:05
 */

namespace kawaii\websocket;


use kawaii\base\Object;

/**
 * Class Response
 * @package kawaii\websocket
 */
class Response extends Object
{
    /**
     * @var
     */
    public $fd;
    /**
     * @var
     */
    public $data;
    /**
     * @var int
     */
    public $opcode = WEBSOCKET_OPCODE_TEXT;
    /**
     * @var bool
     */
    public $finish = true;
}