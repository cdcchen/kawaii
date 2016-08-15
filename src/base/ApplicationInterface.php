<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\base;


use Psr\Http\Message\RequestInterface;

interface ApplicationInterface
{
    public function run();
    
    public function handleRequest(RequestInterface $request);
}