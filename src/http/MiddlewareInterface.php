<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/29
 * Time: 20:41
 */

namespace kawaii\http;


use kawaii\base\ContextInterface;

interface MiddlewareInterface
{
    /**
     * Process a client request and return a response.
     *
     * Takes the incoming request and optionally modifies it before delegating
     * to the next frame to get a response.
     *
     * @param ContextInterface $context
     * @param callable $next
     *
     * @return ContextInterface
     */
    public function __invoke(ContextInterface $context, callable $next): ContextInterface;
}