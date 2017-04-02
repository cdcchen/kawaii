<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 20:37
 */

namespace kawaii\base;


use Kawaii;

/**
 * Class Controller
 * @package kawaii\base
 *
 * @property object $context
 * @property Application $app
 * @property \kawaii\server\BaseServer $server
 */
abstract class Controller extends Object implements ViewContextInterface
{
    /**
     * @var array
     */
    public $id;
    /**
     * @var string
     */
    public $defaultAction;
    /**
     * @var string
     */
    public $layout = 'main';

    /**
     * @var Action
     */
    public $action;

    /**
     * @var string
     */
    private $view;
    /**
     * @var string
     */
    private $viewPath;

    /**
     * @var \kawaii\web\Context|\kawaii\websocket\Context
     */
    protected $context;

    /**
     * Controller constructor.
     * @param string $id
     * @param $context
     * @param array $config
     */
    public function __construct(string $id, $context, array $config = [])
    {
        $this->id = $id;
        $this->context = $context;

        parent::__construct($config);
    }

    /**
     * @return object Controller context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->context->app;
    }

    public function getServer()
    {
        return $this->context->server;
    }

    /**
     * @param string $id
     * @param array $params
     * @return mixed|null
     * @throws InvalidRouteException
     */
    public function runAction(string $id, $params = [])
    {
        $this->action = $this->createAction($id);
        if ($this->action === null) {
            throw new InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
        }

        $result = null;
        if ($this->beforeAction($this->action)) {
            // run the action
            $params = array_merge($this->context->request->getQueryParams(), $params);
            $result = $this->action->runWithParams($params);
            $result = $this->afterAction($this->action, $result);
        }

        return $result;

    }

    /**
     * @param string $route
     * @param array $params
     * @return mixed|null
     */
    public function run(string $route, array $params = [])
    {
        $pos = strpos($route, '/');
        if ($pos === false) {
            return $this->runAction($route, $params);
        } elseif ($pos > 0) {
            // @todo run module action
//            return $this->module->runAction($route, $params);
        } else {
            return $this->context->app->runAction(ltrim($route, '/'), $params);
        }
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return Action
     */
    public function createAction(string $id): Action
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }

        $actionMap = $this->actions();
        if (isset($actionMap[$id])) {
            return Kawaii::createObject($actionMap[$id], [$id, $this]);
        } elseif (preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        }

        return null;
    }

    /**
     * @param Action $action
     * @return bool
     */
    public function beforeAction(Action $action): bool
    {
        return true;
    }

    /**
     * @param Action $action
     * @param mixed $result
     * @return mixed
     * @todo afterAction 待完善
     */
    public function afterAction(Action $action, $result)
    {
        return $result;
    }

    /**
     * @param Action $action
     * @param array $params
     * @return array
     */
    public function bindActionParams(Action $action, array $params): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->id;
    }

    /**
     * Get the route of current action
     */
    public function getRoute(): string
    {
        return $this->action !== null ? $this->action->getUniqueId() : $this->getUniqueId();
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        $content = $this->getView()->render($view, $params, $this);
        return $this->renderContent($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function renderContent(string $content): string
    {
        $layoutFile = $this->findLayoutFile($this->getView());
        if ($layoutFile !== false) {
            return $this->getView()->renderFile($layoutFile, ['content' => $content], $this);
        } else {
            return $content;
        }
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function renderPartial(string $view, array $params = []): string
    {
        return $this->getView()->render($view, $params, $this);
    }

    /**
     * @param string $file
     * @param array $params
     * @return string
     */
    public function renderFile(string $file, array $params = []): string
    {
        return $this->getView()->renderFile($file, $params, $this);
    }


    /**
     * @return View
     */
    public function getView(): View
    {
        if ($this->view === null) {
            $this->view = Kawaii::createObject(View::class);
        }
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        if ($this->viewPath === null) {
            $this->viewPath = $this->context->app->getViewPath() . DIRECTORY_SEPARATOR . $this->id;
        }
        return $this->viewPath;
    }

    /**
     * @param string $path
     */
    public function setViewPath(string $path): void
    {
        $this->viewPath = Kawaii::getAlias($path);
    }

    /**
     * @param View $view
     * @return bool|string
     */
    public function findLayoutFile(View $view)
    {
        if (is_string($this->layout) && $this->layout !== '') {
            $layout = $this->layout;
        } else {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = Kawaii::getAlias($layout);
        } elseif (strncmp($layout, '/', 1) === 0) {
            $file = $this->context->app->getLayoutPath() . DIRECTORY_SEPARATOR . ltrim($layout);
        } else {
            $file = $this->context->app->getLayoutPath() . DIRECTORY_SEPARATOR . ltrim($layout);
            // @todo if use module
//            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $view->defaultExtension;
        if ($view->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }
}