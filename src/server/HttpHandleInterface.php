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
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Interface HttpHandleInterface
 * @package kawaii\server
 */
interface HttpHandleInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param Request $req
     * @param Response $res
     * @return ResponseInterface
     */
    public function handleRequest(
        ServerRequestInterface $request,
        Request $req,
        Response $res
    ): ResponseInterface;
}