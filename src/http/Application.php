<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:14
 */

namespace kawaii\http;


use cdcchen\psr7\Stream;
use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use kawaii\base\ContextInterface;
use kawaii\base\Exception;
use kawaii\base\InvalidConfigException;
use kawaii\base\UserException;
use mars\HandleInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;

/**
 * Class Application
 * @package kawaii\web
 */
class Application extends \kawaii\base\Application implements HandleInterface
{
    use RouterTrait;

    public $staticPath = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var MiddlewareStackInterface
     */
    protected $middleware;


    protected function init(): void
    {
        $seedMiddleware = function (ContextInterface $context) {
            return $context;
        };
        $this->middleware = (new Middleware())->add($seedMiddleware);

        $this->router = new Router();
        $this->loadRoutes();
        $this->hook(new RouteMiddleware($this));
    }

    /**
     * @param ServerRequestInterface $request
     * @param SwooleHttpRequest $req
     * @param SwooleHttpResponse $res
     * @return ResponseInterface
     */
    public function handleRequest(
        ServerRequestInterface $request,
        SwooleHttpRequest $req,
        SwooleHttpResponse $res
    ): ResponseInterface {
        $beginTime = microtime(true);

        $context = new Context($this, $request, new Response(), $req, $res);
        try {
            $exist = $this->handleStaticFiles($context);
            if (!$exist) {
                $context = $this->middleware->handle($context);
            }

        } catch (HttpException $e) {
            $statusCode = $e->statusCode;
            $context->response->write($e->getMessage());
        } catch (UserException $e) {
            $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            $context->response->write($e->getMessage());
        } catch (Exception | \Exception $e) {
            $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            $context->response->write($e->getMessage());
        }

        if (isset($statusCode)) {
            $context->response = $context->response->withStatus($statusCode);
        }

        $finishedTime = microtime(true);
        echo 'Processing the request. time: ', ($finishedTime - $beginTime), PHP_EOL;

        return $context->response;
    }

    /**
     * @param Context $context
     * @return bool
     */
    protected function handleStaticFiles(Context &$context): bool
    {
        // @todo static files process
        foreach ((array)$this->staticPath as $path) {
            $filename = $path . '/' . ltrim($context->request->getUri()->getPath());
            clearstatcache(true, $filename);
            if (!is_file($filename)) {
                continue;
            }

            if (is_readable($filename)) {
                $info = new \finfo(FILEINFO_MIME_TYPE | FILEINFO_SYMLINK);
                $mimeType = $info->file($filename);
                if ($mimeType === false) {
                    // @todo 处理错误日志
                    echo "Get {$filename} mimeType error.\n";
                } else {
                    $context->response = $context->response->withHeader('Content-Type', $mimeType);
                }
                $stream = new Stream(fopen($filename, 'r+'));
                $context->response = $context->response->withBody($stream);
                return true;
            } else {
                $context->response = $context->response
                    ->withStatus(StatusCodeInterface::STATUS_FORBIDDEN, "No permission to read {$filename}");
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function addRoute(
        string $methods,
        string $path,
        callable $handle,
        bool $strict = false,
        string $suffix = ''
    ): Router {
        return $this->router->addRoute($methods, $path, $handle, $strict, $suffix);
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
     * load routes from routes config file.
     */
    protected function loadRoutes(): void
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
    protected function addRouteRule(RouteRule $rule): void
    {
        foreach ($rule->method as $method) {
            $this->router->addRoute($method, $rule->path, $this->buildHandlerByRoute($rule->route), $rule->strict,
                $rule->suffix);
        }
    }

    /**
     * @param string $route
     * @return callable
     */
    protected function buildHandlerByRoute(string $route): callable
    {
        return function (ContextInterface $context, callable $next) use ($route) {
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
                $result = $this->runAction($route, [], $context);
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