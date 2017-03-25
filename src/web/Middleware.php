<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/26
 * Time: 14:28
 */

namespace kawaii\web;


use kawaii\base\Object;
use kawaii\server\MiddlewareStackInterface;

/**
 * Class Middleware
 * @package kawaii\web
 */
class Middleware extends Object implements MiddlewareStackInterface
{
    protected static $middleware;

    /**
     * @param callable $callback
     * @return $this|self
     */
    public function add(callable $callback): self
    {
        if (!is_callable(static::$middleware)) {
            static::$middleware = $callback;
            return $this;
        }

        $next = static::$middleware;
        static::$middleware = function (Context $context) use ($callback, $next) {
            $result = call_user_func($callback, $context, $next);
            return $result;
        };

        return $this;
    }

    /**
     * @param Context $context
     * @return Context|mixed
     */
    public function handle(Context $context): Context
    {
        if (is_callable(static::$middleware)) {
            return call_user_func(static::$middleware, $context);
        }

        return $context;
    }
}