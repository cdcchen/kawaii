<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:14
 */

namespace kawaii\web;


use kawaii\http\HttpServer;
use kawaii\Kawaii;
use RuntimeException;

/**
 * Class Application
 * @package kawaii\web
 */
class Application extends \kawaii\base\Application
{
    use RouterTrait;

    public $routesFile = '@project/config/routes.php';

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var HttpServer
     */
    protected $server;

    /**
     * @var MiddlewareStack
     */
    protected $middlewareStack;

    /**
     * Application constructor.
     * @param array $config Application config
     * @param array $setting Http server setting
     */
    public function __construct($config = [], $setting = [])
    {
        parent::__construct($config);

        Kawaii::$app = $this;
        $this->server = new HttpServer($setting);

        $this->middlewareStack = (new MiddlewareStack())->add(static::buildSeedMiddleware());
        $this->router = new Router();
    }

    /**
     * Run server
     * @return bool
     */
    public function run()
    {
        $this->loadRoutes();
        if (!$this->beforeRun()) {
            throw new RuntimeException('Application::beforeRun must return true or false.');
        }

        $this->hook(new RouteMiddleware($this));

        return $this->server->run($this);
    }

    /**
     * @return bool
     */
    protected function beforeRun()
    {
        return true;
    }

    /**
     * @return object
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function hook(callable $callable)
    {
        $this->middlewareStack->add($callable);
        return $this;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * load routes from routes config file.
     */
    private function loadRoutes()
    {
        $filename = Kawaii::getAlias($this->routesFile);
        $routes = include($filename);

        $routes = array_filter((array)$routes);
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
    private function addRouteRule(RouteRule $rule)
    {
        foreach ($rule->method as $method) {
            $this->router->addRoute($method, $rule->path, $this->buildHandlerByRoute($rule->route), $rule->strict,
                $rule->suffix);
        }
    }

    /**
     * @inheritdoc
     */
    public function addRoute($method, $path, callable $handler, $strict = false, $suffix = '')
    {
        return $this->router->addRoute($method, $path, $handler, $strict, $suffix);
    }

    /**
     * @param string $route
     * @return \Closure
     */
    private function buildHandlerByRoute($route)
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
            $result = $this->runAction($route, $context);
            $context->response->write($result);

            return $context;
        };
    }

    /**
     * @param Request $request
     * @return Context|mixed
     */
    public function handleRequest(Request $request)
    {
        $beginTime = microtime(true);
        echo '------------- Application Begin to process the request.', PHP_EOL;

        $context = new Context($request, new Response());
        try {
            $context = $this->handleMiddleware($context);
        } catch (HttpException $e) {
            $context->response = $context->response->withStatus($e->statusCode);
            $context->response->write($e->getMessage());
        } catch (\Exception $e) {
            $context->response = $context->response->withStatus(500);
            $context->response->write($e->getMessage());
        }
        finally {
            $finishedTime = microtime(true);
            echo '------------- Application finish processing the request. time: ', ($finishedTime - $beginTime), PHP_EOL;
        }

        return $context;
    }

    /**
     * @param Context $context
     * @return Context|mixed
     */
    private function handleMiddleware(Context $context)
    {
        return $this->middlewareStack->handle($context);
    }

    /**
     * Build seed middleware
     */
    private static function buildSeedMiddleware()
    {
        return function (Context $context) {
            return $context;
        };
    }
}