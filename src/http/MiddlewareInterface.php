<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/29
 * Time: 20:41
 */

namespace kawaii\http;


use kawaii\web\Context;

interface MiddlewareInterface
{
    /**
     * Process a client request and return a response.
     *
     * Takes the incoming request and optionally modifies it before delegating
     * to the next frame to get a response.
     *
     * @param Context $context
     * @param callable $next
     *
     * @return Context|mixed
     */
    public function __invoke(Context $context, callable $next);
}