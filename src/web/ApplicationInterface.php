<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\web;


use kawaii\server\BaseServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ApplicationInterface
 * @package kawaii\base
 */
interface ApplicationInterface extends \kawaii\base\ApplicationInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param BaseServer $server
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, BaseServer $server): ResponseInterface;
}