<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\base;


use kawaii\web\Context;
use Psr\Http\Message\RequestInterface;

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
     * @param RequestInterface $request
     * @return Context
     */
    public function handleRequest(RequestInterface $request): Context;

    /**
     * Reload app config
     */
    public function reload(): void;
}