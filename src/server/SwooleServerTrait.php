<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/5/26
 * Time: 00:15
 */

namespace kawaii\server;


use Kawaii;
use kawaii\base\ApplicationInterface;
use kawaii\base\InvalidConfigException;

/**
 * Class SwooleServerTrait
 * @package kawaii\base
 */
trait SwooleServerTrait
{
    public $onMasterStart  = [DefaultCallback::class, 'onMasterStart'];
    public $onMasterStop   = [DefaultCallback::class, 'onMasterStop'];
    public $onManagerStart = [DefaultCallback::class, 'onManagerStart'];
    public $onManagerStop  = [DefaultCallback::class, 'onManagerStop'];
    public $onWorkerStart  = [DefaultCallback::class, 'onWorkerStart'];
    public $onWorkerStop   = [DefaultCallback::class, 'onWorkerStop'];
    public $onWorkerError  = [DefaultCallback::class, 'onWorkerError'];
    public $onTask         = [DefaultCallback::class, 'onTask'];
    public $onFinish       = [DefaultCallback::class, 'onFinish'];
    public $onReceive      = [DefaultCallback::class, 'onReceive'];
    public $onPipeMessage  = [DefaultCallback::class, 'onPipeMessage'];
    public $onConnect      = [DefaultCallback::class, 'onConnect'];
    public $onClose        = [DefaultCallback::class, 'onClose'];


    protected function init(): void
    {
    }

    protected function setCallback(): void
    {
    }

    /**
     * Bind Swoole server event callback
     */
    private function bindCallback(): void
    {
        $this->setCallback();

        if (is_callable($this->onMasterStart)) {
            $this->on('Start', $this->onMasterStart);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('Shutdown', $this->onMasterStop);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('ManagerStart', $this->onManagerStart);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('ManagerStop', $this->onManagerStop);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('WorkerStart', $this->onWorkerStart);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('WorkerStop', $this->onWorkerStop);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('WorkerError', $this->onWorkerError);
        }
        if (is_callable($this->onConnect)) {
            $this->on('Connect', $this->onConnect);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('Close', $this->onClose);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('Task', $this->onTask);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('Finish', $this->onFinish);
        }
        if (is_callable($this->onMasterStart)) {
            $this->on('PipeMessage', $this->onPipeMessage);
        }
        if (is_callable($this->onReceive)) {
            $this->on('Receive', $this->onReceive);
        }
    }

    protected function beforeRun(): void
    {
        $this->init();
        $this->bindCallback();
    }

    public function run(ApplicationInterface $app)
    {
        $app->run();

        $this->beforeRun();
        $this->start();
    }


    ############################## not test ###############################

    /**
     * @var string
     */
    protected $configFile;
    /**
     * @var array
     */
    protected $config = [];

    /**
     * Restart Swoole server
     * @ todo 未完成
     */
    protected function restart(): void
    {
        $this->shutdown();
        static::loadConfig();
    }

    /**
     * @throws InvalidConfigException
     */
    protected function loadConfig(): void
    {
        if (empty($this->configFile)) {
            return;
        }

        if (file_exists($this->configFile)) {
            $this->config = require($this->configFile);
        } else {
            $configFile = $this->configFile;
            throw new InvalidConfigException("Config file: {$configFile} is not exist.");
        }
    }

    /**
     * reload server config
     */
    public function reload(): void
    {
        static::loadConfig();
        $this->reload();
    }

    /**
     * @return array
     */
    private function getSwooleConfig(): array
    {
        return $this->config['swoole'] ?? [];
    }

    /**
     * @param string $filename
     */
    private static function initSwooleLogFile(string $filename): void
    {
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0644, true);
        }

        if ($handle = fopen($filename, 'a')) {
            fclose($handle);
            chmod($filename, 0644); // 需要判断返回值, 然后写日志。
        }
    }

    /**
     * @return string
     */
    public function getPidFile(): string
    {
        return $this->config['pid_file'] ?? (sys_get_temp_dir() . '/kawaii.pid');
    }
}