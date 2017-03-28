<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/2
 * Time: 22:20
 */

namespace kawaii\web;


use cdcchen\psr7\ServerRequest;

/**
 * Class Request
 * @package kawaii\web
 */
class Request extends ServerRequest
{
    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getMethod() === 'PUT';
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->getMethod() === 'HEAD';
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->getMethod() === 'PATCH';
    }

    /**
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->getMethod() === 'OPTIONS';
    }

    /**
     * @return string
     */
    public function getContentLength(): string
    {
        return $this->getHeader('Content-Length');
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->getHeader('Content-Type');
    }

    /**
     * @return string
     * @todo 返回值不正确,此处应该只返回原始数据中的请求路径,可能不包括host
     */
    public function getUrl(): string
    {
        return (string)$this->getUri();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->getUri()->getPath();
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->getUri()->getQuery();
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->getUri()->getHost();
    }
}