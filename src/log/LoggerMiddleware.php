<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/7
 * Time: 20:37
 */

namespace kawaii\log;


use kawaii\base\Object;
use kawaii\server\MiddlewareInterface;
use kawaii\web\Context;
use Psr\Log\LoggerAwareTrait;

class LoggerMiddleware extends Object implements MiddlewareInterface
{
    use LoggerAwareTrait;

    /**
     * @inheritdoc
     */
    public function __invoke(Context $context, callable $next)
    {
        return $next($context);
    }
}