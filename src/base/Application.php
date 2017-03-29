<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/2
 * Time: 15:14
 */

namespace kawaii\base;


use Kawaii;
use kawaii\di\ServiceLocator;
use kawaii\web\Context;

/**
 * Class Application
 * @package kawaii\base
 */
abstract class Application extends ServiceLocator
{
    /**
     * @var
     */
    public $id;
    /**
     * @var string
     */
    public $name = 'My Application';
    /**
     * @var string
     */
    public $version = '1.0.0';
    /**
     * @var string
     */
    public $charset = 'UTF-8';
    /**
     * @var string
     */
    public $language = 'en-US';
    /**
     * @var string
     */
    public $sourceLanguage = 'en-US';
    /**
     * @var
     */
    public $layout = 'main';
    /**
     * @var array
     */
    public $controllerMap = [];
    /**
     * @var string
     */
    public $controllerNamespace = 'app\\controllers';
    /**
     * @var string
     */
    public $defaultRoute = 'site';
    /**
     * @var array
     */
    public $routes = [];
    /**
     * @var array
     */
    public $params = [];

    /**
     * @var string
     */
    protected static $configFile;
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string the root directory that contains view files for this module
     */
    private $viewPath;
    /**
     * @var string the root directory that contains layout view files for this module.
     */
    private $layoutPath;
    /**
     * @var string
     */
    private $vendorPath;


    /**
     * Application constructor.
     * @param string $configFile
     * @param array $config
     */
    public function __construct($configFile, array $config = [])
    {
        Kawaii::$app = $this;

        static::$configFile = $configFile;
        $this->loadConfig();

        parent::__construct(array_merge(static::$config, $config));
    }

    /**
     * @throws InvalidConfigException
     */
    protected function loadConfig()
    {
        if (empty(static::$configFile)) {
            return;
        }

        if (file_exists(static::$configFile)) {
            static::$config = require(static::$configFile);
        } else {
            $configFile = static::$configFile;
            throw new InvalidConfigException("Config file: {$configFile} is not exist.");
        }

        $this->preInit(static::$config);

        $this->registerErrorHandler(static::$config);
    }

    /**
     * Run server
     */
    public function run(): void
    {
        if (!$this->beforeRun()) {
            throw new \RuntimeException('Application::beforeRun must return true or false.');
        }
    }

    /**
     * @return bool
     */
    protected function beforeRun(): bool
    {
        return true;
    }

    /**
     * Reload config
     */
    public function reload(): void
    {
        $this->loadConfig();
    }

    /**
     * @param $config
     * @throws InvalidConfigException
     */
    protected function preInit(&$config)
    {
        if (!isset($config['id'])) {
            throw new InvalidConfigException('The "id" configuration for the Application is required.');
        }

        if (isset($config['basePath'])) {
            $this->setBasePath($config['basePath']);
            unset($config['basePath']);
        } else {
            throw new InvalidConfigException('The "basePath" configuration for the Application is required.');
        }

        if (isset($config['vendorPath'])) {
            $this->setVendorPath($config['vendorPath']);
            unset($config['vendorPath']);
        } else {
            // set "@vendor"
            $this->getVendorPath();
        }

        if (isset($config['runtimePath'])) {
            $this->setRuntimePath($config['runtimePath']);
            unset($config['runtimePath']);
        } else {
            // set "@runtime"
            $this->getRuntimePath();
        }

        if (isset($config['timeZone'])) {
            $this->setTimeZone($config['timeZone']);
            unset($config['timeZone']);
        } elseif (!ini_get('date.timezone')) {
            $this->setTimeZone('UTC');
        }
    }

    protected function bootstrap()
    {

    }

    /**
     * @param $path
     */
    public function setBasePath($path)
    {
        $path = Kawaii::getAlias($path);
        $p = strncmp($path, 'phar://', 7) === 0 ? $path : realpath($path);
        if ($p !== false && is_dir($p)) {
            $this->basePath = $p;
        } else {
            throw new InvalidParamException("The directory does not exist: $path");
        }

        Kawaii::setAlias('@project', $this->getBasePath());
        Kawaii::setAlias('@app', $this->getBasePath() . '/app');
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        if ($this->basePath === null) {
            $class = new \ReflectionClass($this);
            $this->basePath = dirname($class->getFileName());
        }

        return $this->basePath;
    }

    private $_runtimePath;

    /**
     * Returns the directory that stores runtime files.
     * @return string the directory that stores runtime files.
     * Defaults to the "runtime" subdirectory under [[basePath]].
     */
    public function getRuntimePath()
    {
        if ($this->_runtimePath === null) {
            $this->setRuntimePath($this->getBasePath() . DIRECTORY_SEPARATOR . 'runtime');
        }

        return $this->_runtimePath;
    }

    /**
     * Sets the directory that stores runtime files.
     * @param string $path the directory that stores runtime files.
     */
    public function setRuntimePath($path)
    {
        $this->_runtimePath = Kawaii::getAlias($path);
        Kawaii::setAlias('@runtime', $this->_runtimePath);
    }

    /**
     * Returns the directory that contains the view files for this module.
     * @return string the root directory of view files. Defaults to "[[basePath]]/views".
     */
    public function getViewPath()
    {
        if ($this->viewPath === null) {
            $this->viewPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views';
        }
        return $this->viewPath;
    }

    /**
     * Sets the directory that contains the view files.
     * @param string $path the root directory of view files.
     * @throws InvalidParamException if the directory is invalid
     */
    public function setViewPath($path)
    {
        $this->viewPath = Kawaii::getAlias($path);
    }

    /**
     * Returns the directory that contains layout view files for this module.
     * @return string the root directory of layout files. Defaults to "[[viewPath]]/layouts".
     */
    public function getLayoutPath()
    {
        if ($this->layoutPath === null) {
            $this->layoutPath = $this->getViewPath() . DIRECTORY_SEPARATOR . 'layouts';
        }

        return $this->layoutPath;
    }

    /**
     * Sets the directory that contains the layout files.
     * @param string $path the root directory or path alias of layout files.
     * @throws InvalidParamException if the directory is invalid
     */
    public function setLayoutPath($path)
    {
        $this->layoutPath = Kawaii::getAlias($path);
    }

    /**
     * Returns the directory that stores vendor files.
     * @return string the directory that stores vendor files.
     * Defaults to "vendor" directory under [[basePath]].
     */
    public function getVendorPath()
    {
        if ($this->vendorPath === null) {
            $this->setVendorPath($this->getBasePath() . DIRECTORY_SEPARATOR . 'vendor');
        }

        return $this->vendorPath;
    }

    /**
     * Sets the directory that stores vendor files.
     * @param string $path the directory that stores vendor files.
     */
    public function setVendorPath($path)
    {
        $this->vendorPath = Kawaii::getAlias($path);
        Kawaii::setAlias('@vendor', $this->vendorPath);
    }


    /**
     * Returns the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_get().
     * If time zone is not configured in php.ini or application config,
     * it will be set to UTC by default.
     * @return string the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function getTimeZone()
    {
        return date_default_timezone_get();
    }

    /**
     * Sets the time zone used by this application.
     * This is a simple wrapper of PHP function date_default_timezone_set().
     * Refer to the [php manual](http://www.php.net/manual/en/timezones.php) for available timezones.
     * @param string $value the time zone used by this application.
     * @see http://php.net/manual/en/function.date-default-timezone-set.php
     */
    public function setTimeZone($value)
    {
        date_default_timezone_set($value);
    }

    protected function registerErrorHandler(&$config)
    {
        // @todo registerErrorHandler
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed|null
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $defaultValue;
    }

    /**
     * @return mixed
     */
    public function getUniqueId()
    {
        return $this->id;
    }

    /**
     * @param $route
     * @param Context $context
     * @return mixed|null
     * @throws InvalidRouteException
     */
    public function runAction($route, Context $context)
    {
        $parts = $this->createController($route, $context);
        if (is_array($parts)) {
            /* @var $controller Controller */
            list($controller, $actionId) = $parts;
            $result = $controller->runAction($actionId);

            return $result;
        } else {
            throw new InvalidRouteException('Unable to resolve the request "' . $route . '".');
        }
    }

    /**
     * @param $route
     * @param Context $context
     * @return array|bool
     */
    public function createController($route, Context $context)
    {
        $route = trim($route, '/');
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        // double slashes or leading/ending slashes may cause substr problem
        if (strpos($route, '//') !== false) {
            return false;
        }

        if (strpos($route, '/') !== false) {
            list ($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        // module and controller map take precedence
        if (isset($this->controllerMap[$id])) {
//            $controller = Kawaii::createObject($this->controllerMap[$id], [$id, $this]);
            $className = $this->controllerMap[$id];
            $controller = new $className($id, $context);
            return [$controller, $route];
        }

        if (($pos = strrpos($route, '/')) !== false) {
            $id .= '/' . substr($route, 0, $pos);
            $route = substr($route, $pos + 1);
        }

        $controller = $this->createControllerById($id, $context);
        if ($controller === null && $route !== '') {
            $controller = $this->createControllerById($id . '/' . $route, $context);
            $route = '';
        }

        return $controller === null ? false : [$controller, $route];
    }

    /**
     * @param string $id
     * @param Context $context
     * @return null|Controller
     * @throws InvalidConfigException
     */
    public function createControllerById(string $id, Context $context):? Controller
    {
        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        if (!preg_match('%^[a-z][a-z0-9\\-_]*$%', $className)) {
            return null;
        }
        if ($prefix !== '' && !preg_match('%^[a-z0-9_/]+$%i', $prefix)) {
            return null;
        }

        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $className))) . 'Controller';
        $className = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix) . $className, '\\');
        if (strpos($className, '-') !== false || !class_exists($className)) {
            return null;
        }

        if (is_subclass_of($className, 'kawaii\base\Controller')) {
//            $controller = Kawaii::createObject($className, [$id, $this]);
            $controller = new $className($id, $context);
            return get_class($controller) === $className ? $controller : null;
        } elseif (KAWAII_DEBUG) {
            throw new InvalidConfigException("Controller class must extend from \\kawaii\\base\\Controller.");
        } else {
            return null;
        }
    }
}