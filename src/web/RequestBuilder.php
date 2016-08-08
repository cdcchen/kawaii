<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/2
 * Time: 22:20
 */

namespace kawaii\web;


class RequestBuilder extends \kawaii\http\Request
{
    public $clientId;
    public $protocol;
    public $method;
    public $uri;

    const HTTP_EOF = "\r\n\r\n";

    private $rawData   = '';
    private $rawHeader = '';
    private $rawBody   = '';

    private $_cookies;

    private $queryParams;
    private $bodyParams;

    public function __construct($clientId, $data = null)
    {
        $this->clientId = $clientId;

        if ($data !== null) {
            $this->rawData = $data;
        }
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function getRawBody()
    {
        return $this->rawBody;
    }

    public function getRawHeader()
    {
        return $this->rawHeader;
    }

    public function parse()
    {
        list($this->rawHeader, $this->rawBody) = explode(self::HTTP_EOF, $this->rawData);

        $headerLines = explode("\r\n", $this->rawHeader);
        list($method, $uri, $version) = explode(' ', $headerLines[0]);
        unset($headerLines[0]);
        $headers = static::parseHeaderLine($headerLines);
        $this->setHeaders($headers);

        return new Request($method, $uri, $headers, $this->rawBody, $version);
    }

    public function getCookies()
    {
        return $this->_cookies;
    }

    public function getIsGet()
    {
        return strtoupper($this->method) === 'GET';
    }

    public function getIsPost()
    {
        return strtoupper($this->method) === 'POST';
    }

    public function get($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getQueryParams();
        } else {
            return $this->getQueryParam($name, $defaultValue);
        }
    }

    public function post($name = null, $defaultValue = null)
    {
        if ($name === null) {
            return $this->getBodyParams();
        } else {
            return $this->getBodyParam($name, $defaultValue);
        }
    }

    public function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getQueryParams()
    {
        if ($this->queryParams === null) {
            parse_str($this->getQueryString(), $params);
            $this->queryParams = static::filterParams($params);
        }

        return $this->queryParams;
    }

    public function getQueryString()
    {
        return parse_url($this->uri, PHP_URL_QUERY);
    }

    public function getBodyParam($name, $defaultValue = null)
    {
        $params = $this->getBodyParams();

        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    public function getBodyParams()
    {
        if ($this->bodyParams === null) {
            parse_str($this->rawBody, $params);
            $this->bodyParams = static::filterParams($params);
        }
        return $this->bodyParams;
    }

    private static function filterParams($params)
    {
        $filterParams = [];
        foreach ($params as $key => $param) {
            $key = strtolower(trim($key));
            $filterParams[$key] = $param;
        }

        return $filterParams;
    }

    public function parseHeader($rawHeader)
    {
        if (empty($this->rawHeader) && $this->rawData) {
            return $this;
        }

    }

    private static function parseHeaderLine(array $lines)
    {
        $lines = array_filter($lines);
        $headers = [];
        foreach ($lines as $line) {
            $header = explode(':', $line, 2);
            array_walk($header, function (&$item, $key) {
                $item = trim($item);
            });

            $key = ucwords($header[0]);
            $headers[$key] = $header[1] ?: '';
        }

        return $headers;
    }

    private function parseCookies()
    {
        $this->_cookies = static::parseParams($this->_headers['Cookie']);
    }

    private function parseParams($str)
    {
        $params = [];
        $blocks = explode(';', $str);
        foreach ($blocks as $block) {
            $param = explode('=', $block, 2);
            array_walk($param, function (&$value, $key) {
                $value = trim($value);
            });
            $params[$param[0]] = $param[1];
        }

        return $params;
    }

    private function parseRawData()
    {
        list($this->rawHeader, $this->rawBody) = explode(self::HTTP_EOF, $this->rawData);
    }

    private function parseBody()
    {

    }
}