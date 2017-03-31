<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\base;


use kawaii\server\HttpServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ApplicationInterface
 * @package kawaii\base
 */
interface ApplicationInterface
{
    /**
     * @return mixed
     */
    public function run(): void;

    /**
     * @param ServerRequestInterface $request
     * @param HttpServer $server
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, HttpServer $server): ResponseInterface;
}