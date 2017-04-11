<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/30
 * Time: 11:21
 */

namespace kawaii\web;


use kawaii\base\ContextInterface;

interface MiddlewareStackInterface
{
    /**
     * Return an instance with the specified middleware added to the stack.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the stack, and MUST return an instance that contains
     * the specified middleware.
     *
     * @param callable $middleware
     *
     * @return self
     */
    public function add(callable $middleware);

    /**
     * Process the request through middleware and return the response.
     *
     * This method MUST be implemented in such a way as to allow the same
     * stack to be reused for processing multiple requests in sequence.
     *
     * @param ContextInterface $context
     * @return ContextInterface
     */
    public function handle(ContextInterface $context): ContextInterface;
}