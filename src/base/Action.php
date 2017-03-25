<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/3
 * Time: 16:28
 */

namespace kawaii\base;


/**
 * Class Action
 * @package kawaii\base
 */
class Action extends Object
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var Controller
     */
    public $controller;


    /**
     * Action constructor.
     * @param string $id
     * @param Controller $controller
     * @param array $config
     */
    public function __construct(string $id, Controller $controller, array $config)
    {
        $this->id = $id;
        $this->controller = $controller;
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->controller->getUniqueId() . '/' . $this->id;
    }

    /**
     * @param array $params
     * @return mixed|null
     * @throws InvalidConfigException
     */
    public function runWithParams(array $params)
    {
        if (!method_exists($this, 'run')) {
            throw new InvalidConfigException(get_class($this) . ' must define a "run()" method.');
        }
        $args = $this->controller->bindActionParams($this, $params);
        if ($this->beforeRun()) {
            $result = call_user_func_array([$this, 'run'], $args);
            $this->afterRun();

            return $result;
        } else {
            return null;
        }
    }


    /**
     * @return bool whether to run the action.
     */
    protected function beforeRun(): bool
    {
        return true;
    }

    /**
     * After run
     */
    protected function afterRun(): void
    {
    }
}