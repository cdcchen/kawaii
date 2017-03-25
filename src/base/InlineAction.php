<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 16:47
 */

namespace kawaii\base;


class InlineAction extends Action
{
    public $actionMethod;

    /**
     * @param string $id the ID of this action
     * @param Controller $controller the controller that owns this action
     * @param string $actionMethod the controller method that this inline action is associated with
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct(string $id, Controller $controller, string $actionMethod, array $config = [])
    {
        $this->actionMethod = $actionMethod;
        parent::__construct($id, $controller, $config);
    }

    /**
     * Runs this action with the specified parameters.
     * This method is mainly invoked by the controller.
     * @param array $params action parameters
     * @return mixed the result of the action
     */
    public function runWithParams(array $params)
    {
        $args = $this->controller->bindActionParams($this, $params);
        return call_user_func_array([$this->controller, $this->actionMethod], $args);
    }

}