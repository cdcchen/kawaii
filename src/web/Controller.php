<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/1
 * Time: 20:37
 */

namespace kawaii\web;


use kawaii\base\Action;
use kawaii\base\InlineAction;
use kawaii\server\HttpServer;

/**
 * Class Controller
 * @package kawaii\web
 *
 * @property Context $context
 * @property Application $app
 * @property Request $request
 * @property HttpServer $server
 */
class Controller extends \kawaii\base\Controller
{
    public $defaultAction = 'index';

    /**
     * @var array the parameters bound to the current action.
     */
    public $actionParams = [];


    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->context->request;
    }

    /**
     * @param Action $action
     * @param array $params
     * @return array
     */
    public function bindActionParams(Action $action, array $params): array
    {
        if ($action instanceof InlineAction) {
            $method = new \ReflectionMethod($this, $action->actionMethod);
        } else {
            $method = new \ReflectionMethod($action, 'run');
        }

        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array)$params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {
                    throw new \BadMethodCallException("Invalid data received for parameter {$name}.");
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new \BadMethodCallException('Missing required parameters: ' . implode(', ', $missing));
        }

        $this->actionParams = $actionParams;

        return $args;
    }
}