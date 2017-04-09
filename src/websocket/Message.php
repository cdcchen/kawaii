<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 00:59
 */

namespace kawaii\websocket;


use kawaii\base\Object;
use kawaii\server\WebSocketMessageInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Message
 * @package kawaii\websocket
 */
class Message extends Object implements WebSocketMessageInterface
{
    /**
     * @var int
     */
    private $fd;
    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var string
     */
    private $data;
    /**
     * @var int
     */
    private $opCode;

    /**
     * Message constructor.
     * @param int $fd
     * @param ServerRequestInterface $request
     * @param string $data
     * @param int $opCode
     * @param array $config
     */
    public function __construct(int $fd, ServerRequestInterface $request, string $data, int $opCode, array $config = [])
    {
        parent::__construct($config);

        $this->fd = $fd;
        $this->request = $request;
        $this->data = $data;
        $this->opCode = $opCode;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getOpCode(): int
    {
        return $this->opCode;
    }

    /**
     * @return bool
     */
    public function getIsBinary(): bool
    {
        return $this->opCode === WEBSOCKET_OPCODE_BINARY;
    }

    /**
     * @return bool
     */
    public function getIsText(): bool
    {
        return $this->opCode === WEBSOCKET_OPCODE_TEXT;
    }
}