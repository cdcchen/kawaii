<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 16:43
 */

namespace kawaii\web;


use Kawaii;
use kawaii\base\InvalidRouteException;
use kawaii\base\InvalidValueException;
use kawaii\server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RouteMiddleware
 * @package kawaii\web
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @param Context $context
     * @param callable $next
     * @return mixed|ResponseInterface
     * @throws InvalidRouteException
     */
    public function __invoke(Context $context, callable $next): Context
    {
        /* @var Context $context */
        $context = $next($context);

        $path = $context->request->getUri()->getPath();
        $result = Kawaii::$app->getRouter()->dispatch($context->request->getMethod(), $path);
        if ($result === false) {
            $context->response = $context->response->withStatus(404);
            return $context;
        }

        if (is_callable($callable = $result[0])) {
            if ($routeParams = $result[1]) {
                $context->routeParams = $routeParams;
                $params = array_merge($routeParams, $context->request->getQueryParams());
                $context->request = $context->request->withQueryParams($params);
            }

            $context = call_user_func($callable, $context, $next);
            if (!($context instanceof Context)) {
                throw new InvalidValueException('The return value of a route middleware must be the instance of Context.');
            }

        } else {
            throw new InvalidRouteException('Route handler is not callable.');
        }

        return $context;
    }
}