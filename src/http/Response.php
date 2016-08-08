<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/24
 * Time: 10:32
 */

namespace kawaii\http;


use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 * @package kawaii\http
 */
class Response extends Message implements ResponseInterface
{
    /**
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var array Map of standard HTTP status code/reason reasonPhrases
     */
    public static $reasonPhrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'bandwidth limit exceeded',
        510 => 'not extended',
        511 => 'Network Authentication Required',
    ];


    /**
     * Response constructor.
     * @param int $status
     * @param HeaderCollection $headers
     * @param null|mixed $body
     * @param string $version
     * @param null|string $reason
     */
    public function __construct(
        $status = 200,
        HeaderCollection $headers = null,
        $body = null,
        $version = null,
        $reason = null
    ) {
        $this->statusCode = $status;
        $this->setHeaders($headers ?: new HeaderCollection());

        if ($body === '' || $body === null) {
            $this->stream = StreamHelper::createStream($body);
        }

        if (!empty($version)) {
            $this->protocol = $version;
        }

        if (empty($reason) && isset(static::$reasonPhrases[$this->statusCode])) {
            $this->reasonPhrase = static::$reasonPhrases[$this->statusCode];
        } else {
            $this->reasonPhrase = (string)$reason;
        }

        $this->init();
    }

    /**
     * Init after __construct
     */
    protected function init()
    {
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return static
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->statusCode = (int)$code;
        if (empty($reasonPhrase) && isset(static::$reasonPhrases[$new->statusCode])) {
            $reasonPhrase = static::$reasonPhrases[$new->statusCode];
        }
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}