<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/1
 * Time: 16:27
 */

namespace kawaii\websocket;


use Kawaii;
use kawaii\base\ContextInterface;
use kawaii\base\InvalidConfigException;
use kawaii\server\Listener;
use kawaii\server\WebSocketServer;
use kawaii\server\WebSocketHandleInterface;
use kawaii\server\WebSocketMessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\WebSocket\Server;


/**
 * Class Application
 * @package kawaii\websocket
 *
 * @property WebSocketHandleInterface $handle
 */
class Application extends \kawaii\http\Application implements WebSocketHandleInterface
{
    private $enableHttp = true;

    public function http($enable = true)
    {
        $this->enableHttp = $enable;
        return $this;
    }

    public function run(): void
    {
        $this->prepare();

        if (!($this->listener instanceof Listener)) {
            throw new InvalidConfigException('Application::listener must be the instance of ' . Listener::class);
        }

        $server = new WebSocketServer($this->listener->host, $this->listener->port);
        $server->run($this, $this->enableHttp)->start();
    }

    /**
     * @param ServerRequestInterface $req
     * @param Server $server
     */
    public function handleOpen(ServerRequestInterface $req, Server $server): void
    {
        // TODO: Implement handleOpen() method.
    }

    /**
     * @param WebSocketMessageInterface|Message $message
     * @param Server $server
     */
    public function handleMessage(WebSocketMessageInterface $message, Server $server): void
    {
        // @todo Response 实例化未完成
        $response = new Response(['fd' => $message->fd]);
        $context = new Context($this, $message->getConnection(), $message->getRequest(), $response);
        $context = $this->middleware->handle($context);

        /* @var Context $context */
        if (($response = $context->response) instanceof Response) {
            $server->push($response->fd, $response->data, $response->opcode, $response->finish);
            echo "handleMessage - Push message to {$message->getFd()}\n";
        } else {
            echo "handleMessage - No message push.\n";
        }

        echo "This is app handleMessage.\n";
    }

    /**
     * @param Server $server
     * @param int $fd
     */
    public function handleClose(Server $server, int $fd): void
    {
        // TODO: Implement handleClose() method.
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

            if ($context instanceof \kawaii\http\Context) {
                $context->response->write((string)$output . $result);
            } else {
                $context->response->data = (string)$output . $result;
            }

            return $context;
        };
    }
}