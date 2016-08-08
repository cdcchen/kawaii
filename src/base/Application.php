<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/2
 * Time: 15:14
 */

namespace kawaii\base;


use kawaii\di\ServiceLocator;
use kawaii\Kawaii;
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
    public $layout;
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
    public $params = [];

    /**
     * @var string
     */
    private $basePath;
    /**
     * @var string
     */
    private $viewPath;
    /**
     * @var string
     */
    private $layoutPath;


    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->preInit($config);
        $this->registerErrorHandler($config);

        parent::__construct($config);
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

    protected function init()
    {
        parent::init();
        $this->bootstrap();
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
            $controller->bindTo($this);
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
    public function createControllerById($id, Context $context)
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