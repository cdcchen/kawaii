<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:27
 */

namespace kawaii\websocket;


use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use kawaii\base\ApplicationInterface;
use kawaii\base\Exception;
use kawaii\base\InvalidConfigException;
use kawaii\base\UserException;
use kawaii\server\BaseServer;
use kawaii\server\WebSocketHandleInterface;
use kawaii\web\HttpException;
use kawaii\web\Response;
use kawaii\web\Router;
use kawaii\web\RouterTrait;
use kawaii\web\RouteRule;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;


/**
 * Class Application
 * @package kawaii\websocket
 */
class Application extends \kawaii\base\Application implements ApplicationInterface, WebSocketHandleInterface
{
    use RouterTrait;

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
        $this->loadRoutes();
        $this->hook(new RouteMiddleware($this));
    }

    /**
     * Run server
     */
    public function run(): void
    {
        if (!$this->beforeRun()) {
            throw new RuntimeException('Application::beforeRun must return true or false.');
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param BaseServer $server
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, BaseServer $server): ResponseInterface
    {
        $beginTime = microtime(true);

        $context = new Context($this, $request, new Response());
        try {
            $context = $this->middleware->handle($context);
        } catch (HttpException $e) {
            $statusCode = $e->statusCode;
            $context->response->getBody()->write($e->getMessage());
        } catch (UserException $e) {
            $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            $context->response->getBody()->write($e->getMessage());
        } catch (Exception | \Exception $e) {
            $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            $context->response->getBody()->write($e->getMessage());
        }

        if (isset($statusCode)) {
            $context->response = $context->response->withStatus($statusCode);
        }

        $finishedTime = microtime(true);
        echo 'Processing the request. time: ', ($finishedTime - $beginTime), PHP_EOL;

        return $context->response;
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

    /**
     * @return bool
     */
    protected function beforeRun(): bool
    {
        return true;
    }

    /**
     * load routes from routes config file.
     */
    private function loadRoutes(): void
    {
        if (empty($this->config['routes'])) {
            return;
        } elseif (!is_array($this->config['routes'])) {
            throw new InvalidConfigException('Routes config value must be an array.');
        }

        $routes = array_filter($this->config['routes']);
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
    private function buildHandlerByRoute(string $route): \Closure
    {
        return function (Context $context, callable $next) use ($route) {
            /* @var Context $context */
            $context = $next($context);

            if ($route === '*') {
                $route = $context->request->getUri()->getPath();
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

            $context->response->getBody()->write((string)$output . $result);

            return $context;
        };
    }

    /**
     * @inheritdoc
     */
    public function handleOpen(Server $server, Request $request)
    {
        echo "App - handleOpen - Websocket {$request->fd} client connected.\n";
    }

    /**
     * @inheritdoc
     */
    public function handleMessage(Server $server, Frame $frame)
    {
        echo "App - handleMessage - Receive message: {$frame->data} form {$frame->fd}.\n";
    }

    /**
     * @inheritdoc
     */
    public function handleClose(Server $server, int $fd, int $reactorId)
    {
        echo "App - handleClose - WebSocket Client {$fd} from reactor {$reactorId} disconnected.\n";
    }
}