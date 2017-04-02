<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/26
 * Time: 14:28
 */

namespace kawaii\web;


use kawaii\base\InvalidValueException;
use kawaii\base\Object;

/**
 * Class Middleware
 * @package kawaii\web
 */
class Middleware extends Object implements MiddlewareStackInterface
{
    protected $middleware;

    /**
     * @param callable $callback
     * @return $this|self
     */
    public function add(callable $callback): self
    {
        if (!is_callable($this->middleware)) {
            $this->middleware = $callback;
            return $this;
        }

        $next = $this->middleware;
        $this->middleware = function (Context $context) use ($callback, $next) {
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
        if (is_callable($this->middleware)) {
            return call_user_func($this->middleware, $context);
        } else {
            throw new InvalidValueException('The middleware is not callable');
        }
    }
}