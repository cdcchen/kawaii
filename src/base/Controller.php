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

class Controller extends Object
{
    public $id;
    public $defaultAction;
    public $layout;
    public $action;

    private $view;
    private $viewPath;

    private $context;

    public function __construct($id, Context $context, $config = [])
    {
        $this->id = $id;
        $this->context = $context;
        parent::__construct($config);
    }

    public function getRequest()
    {
        return $this->context->request;
    }

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

    public function run($route)
    {
        $action = $route;
        return $this->runAction($action);
    }

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

    public function beforeAction($action)
    {
//        $event = new ActionEvent($action);
//        $this->trigger(self::EVENT_BEFORE_ACTION, $event);
//        return $event->isValid;
        return true;
    }

    public function afterAction($action, $result)
    {
//        $event = new ActionEvent($action);
//        $event->result = $result;
//        $this->trigger(self::EVENT_AFTER_ACTION, $event);
//        return $event->result;
        return $result;
    }

    public function bindActionParams($action, $params)
    {
        return [];
    }

    public function getUniqueId()
    {
        return $this->id;
    }

    public function getRoute()
    {
//        return $this->action !== null ? $this->action->getUniqueId() : $this->getUniqueId();
    }

    public function render($view, $params = [])
    {
//        $content = $this->getView()->render($view, $params, $this);
//        return $this->renderContent($content);
    }

    public function renderContent($content)
    {
//        $layoutFile = $this->findLayoutFile($this->getView());
//        if ($layoutFile !== false) {
//            return $this->getView()->renderFile($layoutFile, ['content' => $content], $this);
//        } else {
//            return $content;
//        }
    }

    public function renderPartial($view, $params = [])
    {
//        return $this->getView()->render($view, $params, $this);
    }

    public function renderFile($file, $params = [])
    {
//        return $this->getView()->renderFile($file, $params, $this);
    }

    public function getView()
    {
        if ($this->view === null) {
//            $this->view = Kawaii::$app->getView();
        }
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getViewPath()
    {
        if ($this->viewPath === null) {
//            $this->viewPath = $this->module->getViewPath() . DIRECTORY_SEPARATOR . $this->id;
        }
        return $this->viewPath;
    }

    public function setViewPath($path)
    {
//        $this->_viewPath = Kawaii::getAlias($path);
    }

    public function findLayoutFile($view)
    {
//        $module = $this->module;
//        if (is_string($this->layout)) {
//            $layout = $this->layout;
//        } elseif ($this->layout === null) {
//            while ($module !== null && $module->layout === null) {
//                $module = $module->module;
//            }
//            if ($module !== null && is_string($module->layout)) {
//                $layout = $module->layout;
//            }
//        }
//
//        if (!isset($layout)) {
//            return false;
//        }
//
//        if (strncmp($layout, '@', 1) === 0) {
//            $file = Kawaii::getAlias($layout);
//        } elseif (strncmp($layout, '/', 1) === 0) {
//            $file = Kawaii::$app->getLayoutPath() . DIRECTORY_SEPARATOR . substr($layout, 1);
//        } else {
//            $file = $module->getLayoutPath() . DIRECTORY_SEPARATOR . $layout;
//        }
//
//        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
//            return $file;
//        }
//        $path = $file . '.' . $view->defaultExtension;
//        if ($view->defaultExtension !== 'php' && !is_file($path)) {
//            $path = $file . '.php';
//        }
//
//        return $path;
    }
}