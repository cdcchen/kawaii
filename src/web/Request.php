<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/2
 * Time: 22:20
 */

namespace kawaii\web;


use kawaii\http\CookieParser;
use kawaii\http\HeaderParser;
use kawaii\http\ServerRequest;

class Request extends ServerRequest
{
    /**
     * @param string $data
     * @return static
     */
    public static function create($data)
    {
        list($header, $body) = explode(self::HTTP_EOF, $data);

        $headerLines = explode(self::HEADER_LINE_EOF, $header);
        list($method, $uri, $version) = explode(' ', trim($headerLines[0]));
        unset($headerLines[0]);
        $headers = HeaderParser::parse($headerLines);
        $serverParams = ServerParams::create(); // @todo 暂用

        $uri = '/' . ltrim($uri, '/');
        $serverRequest = new static($method, $uri, $headers, $body, $version, $serverParams);

        if (function_exists('mb_parse_str')) {
            mb_parse_str($serverRequest->getUri()->getQuery(), $queryParams);
        } else {
            parse_str($serverRequest->getUri()->getQuery(), $queryParams);
        }
        $cookieParams = CookieParser::parse($serverRequest->getHeaderLine('cookie'));

        if (function_exists('mb_parse_str')) {
            mb_parse_str($serverRequest->getBody(), $parsedBody);
        } else {
            parse_str((string)$serverRequest->getBody(), $parsedBody);
        }

        $uploadedFiles = [];

        return $serverRequest->withCookieParams($cookieParams)
                             ->withQueryParams($queryParams)
                             ->withParsedBody($parsedBody)
                             ->withUploadedFiles($uploadedFiles);
    }

    ################### Get header short methods ######################

    public function getIsPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function getContentLength()
    {
        return $this->getHeader('Content-Length');
    }

    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }

    /**
     * @return string
     * @todo 返回值不正确,此处应该只返回原始数据中的请求路径,可能不包括host
     */
    public function getUrl()
    {
        return (string)$this->getUri();
    }

    public function getPath()
    {
        return $this->getUri()->getPath();
    }

    public function getQueryString()
    {
        return $this->getUri()->getQuery();
    }

    public function getHost()
    {
        return $this->getUri()->getHost();
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
}