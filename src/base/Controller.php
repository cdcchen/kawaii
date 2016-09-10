<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 20:37
 */

namespace kawaii\base;


use Kawaii;
use kawaii\web\Context;

class Controller extends Object implements ViewContextInterface
{
    public $id;
    public $defaultAction;
    public $layout = 'main';

    /**
     * @var Action
     */
    public $action;

    private $view;
    private $viewPath;

    private $context;

    /**
     * Controller constructor.
     * @param array $id
     * @param Context $context
     * @param array $config
     */
    public function __construct($id, Context $context, $config = [])
    {
        $this->id = $id;
        $this->context = $context;
        parent::__construct($config);
    }

    /**
     * @return \kawaii\web\Request|\Psr\Http\Message\RequestInterface
     */
    public function getRequest()
    {
        return $this->context->request;
    }

    /**
     * @param $id
     * @return mixed|null
     * @throws InvalidRouteException
     */
    public function runAction($id)
    {
        $this->action = $this->createAction($id);
        if ($this->action === null) {
            throw new InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
        }

        $result = null;

        if ($this->beforeAction($this->action)) {
            // run the action
            $params = $this->context->request->getQueryParams();
            $result = $this->action->runWithParams($params);
            $result = $this->afterAction($this->action, $result);
        }

        return $result;

    }

    /**
     * @param $route
     * @return mixed|null
     */
    public function run($route)
    {
        $action = $route;
        return $this->runAction($action);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [];
    }

    /**
     * @param $id
     * @return Action
     */
    public function createAction($id)
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
     * @param $action
     * @return bool
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     * @param $action
     * @param $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        return $result;
    }

    /**
     * @param $action
     * @param $params
     * @return array
     */
    public function bindActionParams($action, $params)
    {
        return [];
    }

    /**
     * @return array
     */
    public function getUniqueId()
    {
        return $this->id;
    }

    /**
     * Get the route of current action
     */
    public function getRoute()
    {
        return $this->action !== null ? $this->action->getUniqueId() : $this->getUniqueId();
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        $content = $this->getView()->render($view, $params, $this);
        return $this->renderContent($content);
    }

    /**
     * @param string $content
     * @return string
     */
    public function renderContent($content)
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
    public function renderPartial($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }

    /**
     * @param string $file
     * @param array $params
     * @return string
     */
    public function renderFile($file, $params = [])
    {
        return $this->getView()->renderFile($file, $params, $this);
    }


    /**
     * @return View
     */
    public function getView()
    {
        if ($this->view === null) {
            $this->view = Kawaii::createObject(View::className());
        }
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getViewPath()
    {
        if ($this->viewPath === null) {
            $this->viewPath = Kawaii::$app->getViewPath() . DIRECTORY_SEPARATOR . $this->id;
        }
        return $this->viewPath;
    }

    /**
     * @param string $path
     */
    public function setViewPath($path)
    {
        $this->viewPath = Kawaii::getAlias($path);
    }

    /**
     * @param View $view
     * @return bool|string
     */
    public function findLayoutFile($view)
    {
        if (is_string($this->layout) && $this->layout !== '') {
            $layout = $this->layout;
        } else {
            return false;
        }

        if (strncmp($layout, '@', 1) === 0) {
            $file = Kawaii::getAlias($layout);
        } elseif (strncmp($layout, '/', 1) === 0) {
            $file = Kawaii::$app->getLayoutPath() . DIRECTORY_SEPARATOR . ltrim($layout);
        } else {
            $file = Kawaii::$app->getLayoutPath() . DIRECTORY_SEPARATOR . ltrim($layout);
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