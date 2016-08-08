<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/4
 * Time: 18:59
 */

namespace kawaii\web;


use kawaii\http\Cookie;
use kawaii\http\CookieCollection;

/**
 * Class Response
 * @package kawaii\web
 */
class Response extends \kawaii\http\Response
{
    const FORMAT_RAW   = 'raw';
    const FORMAT_HTML  = 'html';
    const FORMAT_JSON  = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML   = 'xml';

    /**
     * @var string
     */
    public $format = self::FORMAT_HTML;
    /**
     * @var string
     */
    public $version = '1.1';
    /**
     * @var string
     */
    public $charset = 'utf-8';

//    public $formatters = [];
//
//    private static $defaultFormatters = [
//        self::FORMAT_HTML => 'kawaii\web\HtmlResponseFormatter',
//        self::FORMAT_XML => 'kawaii\web\XmlResponseFormatter',
//        self::FORMAT_JSON => 'kawaii\web\JsonResponseFormatter',
//        self::FORMAT_JSONP => [
//            'class' => 'kawaii\web\JsonResponseFormatter',
//            'useJsonp' => true,
//        ],
//    ];

    /**
     * @var CookieCollection
     */
    protected $cookies;

    protected function init()
    {
        parent::init();
//        $this->formatters = array_merge($this->formatters, static::$defaultFormatters);
    }

    /**
     * Response::getBody()->write() shortcut
     *
     * @param $string
     */
    public function write($string)
    {
        $this->getBody()->write($string);
    }

    /**
     * @param string|Cookie $cookie
     * @param string $value
     * @param int $expires
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return static
     */
    public function addCookie(
        $cookie,
        $value = '',
        $expires = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $httpOnly = true
    ) {
        if (is_string($cookie)) {
            $cookie = new Cookie($cookie, $value, $expires, $path, $domain, $secure, $httpOnly);
        }

        if (!($cookie instanceof Cookie)) {
            throw new \InvalidArgumentException('First argument must be a string or Cookie object.');
        }

        $this->getCookies()->add($cookie);

        return $this;
    }

    /**
     * @return CookieCollection
     */
    public function getCookies()
    {
        if ($this->cookies === null) {
            $this->cookies = new CookieCollection();
        }

        return $this->cookies;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $response = $this->prepare();
        if ($response->getCookies()->count() > 0) {
            $response = $response->withAddedHeader('Set-Cookie', $this->getCookies()->getValues());
        }

        return $response->buildContent();
    }

    /**
     * The preparing work before output
     * @return $this MUST BE return the instance of \kawaii\web\Response
     */
    protected function prepare()
    {
        return $this;
    }

    /**
     * @return string
     */
    private function buildHeaders()
    {
        return "HTTP/{$this->protocol} {$this->statusCode} {$this->reasonPhrase}" . (string)$this->headers;
    }

    /**
     * @return string
     */
    private function buildContent()
    {
        return $this->buildHeaders() . self::HTTP_EOF . (string)$this->getBody();
    }

}