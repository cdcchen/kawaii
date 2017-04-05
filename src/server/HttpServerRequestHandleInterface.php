<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\server;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface HttpServerRequestHandleInterface
 * @package kawaii\server
 */
interface HttpServerRequestHandleInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param BaseServer $server
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, BaseServer $server): ResponseInterface;
}