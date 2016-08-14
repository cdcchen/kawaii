<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 16:43
 */

namespace kawaii\web;


use kawaii\base\InvalidRouteException;
use kawaii\base\InvalidValueException;
use kawaii\http\MiddlewareInterface;
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
    public function __invoke(Context $context, callable $next)
    {
        /* @var Context $context */
        $context = $next($context);
        $request = $context->request;

        $path = ltrim($request->getUri()->getPath(), '/');
        $result = \Kawaii::$app->getRouter()->dispatch($request->getMethod(), $path);
        if ($result[0] === Router::ROUTE_NOT_FOUND) {
            $context->response = $context->response->withStatus(404);
            return $context;
        }

        $callable = $result[1];
        if (is_callable($callable)) {
            if ($result[2]) {
                $context->routeParams = $result[2];
                $params = array_merge($context->routeParams, $request->getQueryParams());
                $context->request = $request->withQueryParams($params);
            }
            $context = call_user_func($result[1], $context, $next);
            if (!($context instanceof Context)) {
                throw new InvalidValueException('The return value of a route middleware must be the instance of Context.');
            }

        } else {
            throw new InvalidRouteException('Route handler is not callable.');
        }

        return $context;
    }
}