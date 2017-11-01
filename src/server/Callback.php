<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/10
 * Time: 17:24
 */

namespace kawaii\server;


use cdcchen\psr7\HeaderCollection;
use cdcchen\psr7\Response;
use cdcchen\psr7\ServerRequest;
use Fig\Http\Message\StatusCodeInterface;
use Kawaii;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\Http\Response as SwooleHttpResponse;
use Swoole\Server;

/**
 * Class HttpCallback
 * @package kawaii\server
 */
class Callback
{
    /**
     * @var HandleInterface
     */
    protected $requestHandle;

    public function setRequestHandle(HandleInterface $handle)
    {
        $this->requestHandle = $handle;
        return $this;
    }

    /**
     * bind callback
     * @param Server $server
     */
    public function bind(Server $server): void
    {
        $server->on('Start', [$this, 'onMasterStart']);
        $server->on('Shutdown', [$this, 'onMasterStop']);
        $server->on('ManagerStart', [$this, 'onManagerStart']);
        $server->on('ManagerStop', [$this, 'onManagerStop']);
        $server->on('WorkerStart', [$this, 'onWorkerStart']);
        $server->on('WorkerStop', [$this, 'onWorkerStop']);
        $server->on('WorkerError', [$this, 'onWorkerError']);
        $server->on('PipeMessage', [$this, 'onPipeMessage']);
        $server->on('Task', [$this, 'onTask']);
        $server->on('Finish', [$this, 'onFinish']);
        $server->on('Request', [$this, 'onRequest']);
    }

    /**
     * @param SwooleHttpRequest $req
     * @param SwooleHttpResponse $res
     */
    public function onRequest(SwooleHttpRequest $req, SwooleHttpResponse $res): void
    {
        try {
            $request = static::buildServerRequest($req);

            // App handle request
            $response = $this->requestHandle->handleRequest($request, $req, $res);

            if (!($response instanceof Response)) {
                $response = static::buildServerErrorResponse('requestHandle must be return an instance of \cdcchen\psr7\Response');
            }

        } catch (\Exception $e) {
            $message = $e->getFile() . PHP_EOL . $e->getLine() . PHP_EOL . $e->getMessage();
            $response = new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $message);
            echo "Exception occurred: {$e->getMessage()}\n";
        }

        foreach ($response->getHeaders() as $name => $values) {
            $res->header($name, $response->getHeaderLine($name));
        }

        $res->status($response->getStatusCode());
        $res->end((string)$response->getBody());
    }

    /**
     * @param SwooleHttpRequest $req
     * @return ServerRequest
     */
    public static function buildServerRequest(SwooleHttpRequest $req): ServerRequest
    {
        $request = new ServerRequest(
            $req->server['request_method'],
            $req->server['request_uri'],
            new HeaderCollection(empty($req->header) ? [] : $req->header),
            $req->rawContent(),
            '1.1',
            $req->server
        );
        $request = $request->withQueryParams($req->get ?? [])
                           ->withCookieParams($req->cookie ?? []);
        if (isset($req->post)) {
            $request = $request->withParsedBody($req->post ?? []);
        }
        if (isset($req->files)) {
            $request = $request->withUploadedFiles(static::buildUploadedFiles($req->files));
        };

        return $request;
    }


    /**
     * @param array $files
     * @return UploadedFileInterface[]
     */
    protected static function buildUploadedFiles(array $files): array
    {
        return [];
    }

    /**
     * @param string $message
     * @return ResponseInterface
     */
    protected static function buildServerErrorResponse($message): ResponseInterface
    {
        return new Response(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, null, $message);
    }

    /**
     * @param Server|HttpServer $server
     */
    public function onMasterStart(Server $server): void
    {
        $server->setProcessName('master process');
        echo "Master pid: {$server->master_pid} starting...\n";

        $hook = $server->createHook('ServerOnMasterStart');
        if ($hook instanceof ServerHookInterface) {
            $hook->run($server);
        } elseif ($hook !== null) {
            throw new \UnexpectedValueException('ServerOnMasterStart must implement the ServerHookInterface');
        }
    }

    /**
     * @param Server|HttpServer $server
     */
    public function onMasterStop(Server $server): void
    {
        echo "Master pid: {$server->master_pid} shutdown...\n";

        $hook = $server->createHook('ServerOnMasterStop');
        if ($hook instanceof ServerHookInterface) {
            $hook->run($server);
        } elseif ($hook !== null) {
            throw new \UnexpectedValueException('ServerOnMasterStop must implement the ServerHookInterface');
        }
    }

    /**
     * @param Server|HttpServer $server
     */
    public function onManagerStart(Server $server): void
    {
        $server->setProcessName('manager');

        echo "Manager pid: {$server->manager_pid} starting...\n";
    }

    /**
     * @param Server $server
     */
    public function onManagerStop(Server $server): void
    {
        echo "Manager pid: {$server->manager_pid} stopped...\n";
    }

    /**
     * @param Server|HttpServer $server
     * @param int $workId
     */
    public function onWorkerStart(Server $server, int $workId): void
    {
        $server->setProcessName($server->taskworker ? 'task' : 'worker');

        // @todo 需要重新载入配置

        echo ($server->taskworker ? 'task' : 'worker') . ": $workId starting...\n";

        $hook = $server->createHook('ServerOnWorkerStart');
        if ($hook instanceof WorkerHookInterface) {
            $hook->run($server, $workId);
        } elseif ($hook !== null) {
            throw new \UnexpectedValueException('ServerOnWorkerStart must implement the ServerHookInterface');
        }
    }

    /**
     * @param Server|HttpServer $server
     * @param int $workId
     */
    public function onWorkerStop(Server $server, int $workId): void
    {
        echo "Worker: $workId stopped...\n";

        $hook = $server->createHook('ServerOnWorkerStop');
        if ($hook instanceof WorkerHookInterface) {
            $hook->run($server, $workId);
        } elseif ($hook !== null) {
            throw new \UnexpectedValueException('ServerOnWorkerStop must implement the ServerHookInterface');
        }
    }

    /**
     * @param Server $server
     * @param int $workerId
     * @param int $workerPid
     * @param int $exitCode
     * @param int $signal
     */
    public function onWorkerError(
        Server $server,
        int $workerId,
        int $workerPid,
        int $exitCode,
        int $signal
    ): void {
        echo "Worker error: id {$workerId}, pid {$workerPid}, exit code {$exitCode}, signal: {$signal}.\n";
    }

    /**
     * @param Server|HttpServer $server
     * @param int $taskId
     * @param int $fromWorkerId
     * @param $data
     */
    public function onTask(Server $server, int $taskId, int $fromWorkerId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->handle($server, $taskId);
        }

        echo "Task {$taskId} starting, worker {$fromWorkerId}.\n";
    }

    /**
     * @param Server|HttpServer $server
     * @param int $taskId
     * @param $data
     */
    public function onFinish(Server $server, int $taskId, $data): void
    {
        if ($data instanceof BaseTask) {
            $data->done();
        }

        echo "Task {$taskId} run finished.\n";
    }

    /**
     * @param Server|HttpServer $server
     * @param int $fromWorkerId
     * @param $message
     */
    public function onPipeMessage(Server $server, int $fromWorkerId, $message): void
    {
        echo "Receive message: {$message} from worker {$fromWorkerId}.\n";
    }
}