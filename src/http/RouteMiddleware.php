<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 16:43
 */

namespace kawaii\http;


use Kawaii;
use kawaii\base\ContextInterface;
use kawaii\base\InvalidRouteException;
use kawaii\base\InvalidValueException;
use kawaii\http\Context as HttpContext;
use kawaii\websocket\Context as WebSocketContext;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RouteMiddleware
 * @package kawaii\web
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param ContextInterface $context
     * @param callable $next
     * @return mixed|ResponseInterface
     * @throws InvalidRouteException
     */
    public function __invoke(ContextInterface $context, callable $next): ContextInterface
    {
        /* @var Context $context */
        $context = $next($context);

        if ($context instanceof HttpContext) {
            return $this->handleHttpContext($context, $next);
        } elseif ($context instanceof WebSocketContext) {
            return $this->handleWebSocketContext($context, $next);
        } else {
            throw new InvalidValueException("No {$context->className()} handle.");
        }
    }

    private function handleHttpContext(HttpContext $context, callable $next): HttpContext
    {
        $path = $context->request->getUri()->getPath();
        $result = $this->app->getRouter()->dispatch($context->request->getMethod(), $path);
        if ($result === false) {
            $context->response = $context->response->withStatus(404);
            return $context;
        }

        [$callable, $routeParams] = $result;
        if (is_callable($callable)) {
            if ($routeParams) {
                $context->routeParams = $routeParams;
                $params = array_merge($routeParams, $context->request->getQueryParams());
                $context->request = $context->request->withQueryParams($params);
            }

            $context = call_user_func($callable, $context, $next);
            if (!($context instanceof HttpContext)) {
                throw new InvalidValueException('The return value of a route middleware must be the instance of Context.');
            }

        } else {
            throw new InvalidRouteException('Route handler is not callable.');
        }

        return $context;
    }

    private function handleWebSocketContext(WebSocketContext $context, callable $next): WebSocketContext
    {
        $path = $context->request->getUri()->getPath();
        $result = $this->app->getRouter()->dispatch($context->request->getMethod(), $path);
        if ($result === false) {
            $context->response->data = 'No route';
            return $context;
        }

        [$callable, $routeParams] = $result;
        if (is_callable($callable)) {
            if ($routeParams) {
                $context->routeParams = $routeParams;
                $params = array_merge($routeParams, $context->request->getQueryParams());
                $context->request = $context->request->withQueryParams($params);
            }

            $context = call_user_func($callable, $context, $next);
            if (!($context instanceof WebSocketContext)) {
                throw new InvalidValueException('The return value of a route middleware must be the instance of Context.');
            }

        } else {
            throw new InvalidRouteException('Route handler is not callable.');
        }

        return $context;
    }
}