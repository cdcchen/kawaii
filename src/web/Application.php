<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:14
 */

namespace kawaii\web;


use cdcchen\psr7\Stream;
use Closure;
use Kawaii;
use kawaii\base\ApplicationInterface;
use kawaii\base\Exception;
use kawaii\base\InvalidConfigException;
use kawaii\base\UserException;
use Psr\Http\Message\RequestInterface;

/**
 * Class Application
 * @package kawaii\web
 */
class Application extends \kawaii\base\Application implements ApplicationInterface
{
    use RouterTrait;

    public $staticPath = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Middleware
     */
    protected $middleware;


    protected function init(): void
    {
        $seedMiddleware = function (Context $context) {
            return $context;
        };
        $this->middleware = (new Middleware())->add($seedMiddleware);

        $this->router = new Router();
    }

    /**
     * Run server
     */
    protected function beforeRun(): bool
    {
        $this->loadRoutes();
        $this->hook(new RouteMiddleware());

        return true;
    }

    /**
     * @param RequestInterface $request
     * @return Context
     */
    public function handleRequest(RequestInterface $request): Context
    {
        $beginTime = microtime(true);

        $context = new Context($request, new Response());
        try {
            // @todo static files process
            foreach ((array)Kawaii::$app->staticPath as $path) {
                $filename = $path . '/' . ltrim($request->getUri()->getPath());
                clearstatcache(true, $filename);
                if (is_file($filename) && is_readable($filename)) {
                    $stream = new Stream(fopen($filename, 'r+'));
                    $context->response = $context->response->withBody($stream);

                    return $context;
                }
            }

            $context = $this->middleware->handle($context);
        } catch (HttpException $e) {
            $statusCode = $e->statusCode;
            $context->response->write($e->getMessage());
        } catch (UserException $e) {
            $statusCode = 500;
            $context->response->write($e->getMessage());
        } catch (Exception | \Exception $e) {
            $statusCode = 500;
            $context->response->write($e->getMessage());
        }
        finally {
            $finishedTime = microtime(true);
            echo 'Processing the request. time: ', ($finishedTime - $beginTime), PHP_EOL;
        }

        if (isset($statusCode)) {
            $context->response = $context->response->withStatus($statusCode);
        }

        return $context;
    }

    /**
     * @inheritdoc
     */
    public function addRoute(
        string $methods,
        string $path,
        callable $handler,
        bool $strict = false,
        string $suffix = ''
    ): Router {
        return $this->router->addRoute($methods, $path, $handler, $strict, $suffix);
    }

    /**
     * @param callable $callable
     * @return $this|self
     */
    public function hook(callable $callable): self
    {
        $this->middleware->add($callable);
        return $this;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    public function reload(): void
    {
        parent::reload();
        $this->loadRoutes();
    }

    /**
     * load routes from routes config file.
     */
    private function loadRoutes(): void
    {
        if (empty(static::$config['routes'])) {
            return;
        } elseif (!is_array(static::$config['routes'])) {
            throw new InvalidConfigException('Routes config value must be an array.');
        }

        $routes = array_filter(static::$config['routes']);
        foreach ($routes as $path => $route) {
            $ruleConfig = [];
            if (is_array($route)) {
                $ruleConfig = $route;
                $route = $ruleConfig[0];
                unset($ruleConfig[0]);
            }

            $rule = new RouteRule($path, $route, $ruleConfig);
            $this->addRouteRule($rule);
        }
    }

    /**
     * @param RouteRule $rule
     */
    private function addRouteRule(RouteRule $rule): void
    {
        foreach ($rule->method as $method) {
            $this->router->addRoute($method, $rule->path, $this->buildHandlerByRoute($rule->route), $rule->strict,
                $rule->suffix);
        }
    }

    /**
     * @param string $route
     * @return \Closure
     */
    private function buildHandlerByRoute(string $route): Closure
    {
        return function (Context $context, callable $next) use ($route) {
            /* @var Context $context */
            $context = $next($context);

            if ($route === '*') {
                $route = $context->request->getPath();
            } else {
                $placers = [
                    '{controller}' => $context->getRouteParam('controller', ''),
                    '{action}' => $context->getRouteParam('action', ''),
                ];
                $route = strtr($route, $placers);
            }

            try {
                ob_start();
                ob_implicit_flush(false);
                $result = $this->runAction($route, $context);
            } catch (\Exception $e) {
                $result = $e->getMessage();
            }
            finally {
                $output = ob_get_clean();
            }

            $context->response->write((string)$output . $result);

            return $context;
        };
    }
}