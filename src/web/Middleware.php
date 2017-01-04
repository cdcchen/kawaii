<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/26
 * Time: 14:28
 */

namespace kawaii\web;


use kawaii\base\Object;
use kawaii\http\MiddlewareStackInterface;

/**
 * Class Middleware
 * @package kawaii\web
 */
class Middleware extends Object implements MiddlewareStackInterface
{
    protected static $middleware;

    /**
     * @param callable $callback
     * @return $this
     */
    public function add(callable $callback)
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
    public function handle(Context $context)
    {
        if (is_callable(static::$middleware)) {
            return call_user_func(static::$middleware, $context);
        }

        return $context;
    }
}