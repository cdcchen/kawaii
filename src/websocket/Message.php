<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/9
 * Time: 00:59
 */

namespace kawaii\websocket;


use kawaii\base\Object;
use kawaii\server\Connection;
use kawaii\server\WebSocketMessageInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Message
 * @package kawaii\websocket
 *
 * @property ServerRequestInterface $request
 * @property int $fd
 * @property Connection $connection
 * @property int $opcode
 * @property string $data
 * @property bool $isBinary
 * @property bool $isText
 */
class Message extends Object implements WebSocketMessageInterface
{
    /**
     * @var Connection
     */
    private $connection;
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
    private $opcode;

    /**
     * Message constructor.
     * @param Connection $connection
     * @param ServerRequestInterface $request
     * @param string $data
     * @param int $opcode
     * @param array $config
     */
    public function __construct(
        Connection $connection,
        ServerRequestInterface $request,
        string $data,
        int $opcode,
        array $config = []
    ) {
        parent::__construct($config);

        $this->connection = $connection;
        $this->request = $request;
        $this->data = $data;
        $this->opcode = $opcode;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->connection->fd;
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
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * @return bool
     */
    public function getIsBinary(): bool
    {
        return $this->opcode === WEBSOCKET_OPCODE_BINARY;
    }

    /**
     * @return bool
     */
    public function getIsText(): bool
    {
        return $this->opcode === WEBSOCKET_OPCODE_TEXT;
    }
}