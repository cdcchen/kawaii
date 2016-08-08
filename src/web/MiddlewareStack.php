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
use SplDoublyLinkedList;

/**
 * Class Middleware
 * @package kawaii\web
 */
class MiddlewareStack extends Object implements MiddlewareStackInterface
{
    /**
     * @var SplDoublyLinkedList
     */
    private $stack;

    /**
     * Middleware constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->stack = new SplDoublyLinkedList();
        $this->stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP);
    }

    /**
     * @inheritdoc
     */
    public function add(callable $middleware)
    {
        if ($this->stack->isEmpty()) {
            $this->stack[] = $middleware;
            return $this;
        }

        $next = $this->stack->top();
        $this->stack[] = function (Context $context) use ($middleware, $next) {
            $result = call_user_func($middleware, $context, $next);

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
        if ($this->stack->isEmpty()) {
            return $context;
        }

        $callable = $this->stack->top();

        return $callable($context);
    }
}