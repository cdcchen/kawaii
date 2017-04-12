<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/2
 * Time: 22:20
 */

namespace kawaii\http;



use cdcchen\psr7\CookieParser;
use cdcchen\psr7\HeaderParser;
use cdcchen\psr7\ServerRequest;

class Request extends ServerRequest
{
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