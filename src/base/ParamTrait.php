<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/12
 * Time: 20:48
 */

namespace kawaii\base;


/**
 * Class ParamTrait
 * @package kawaii\base
 */
trait ParamTrait
{
    /**
     * @var array
     */
    private $params = [];

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return ParamTrait|static
     */
    public function setParams(array $params): self
    {
        foreach ($params as $name => $value) {
            $this->params[$name] = $value;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     * @return ParamTrait|static
     */
    public function setParam(string $name, $value): self
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getParam(string $name, $defaultValue = null)
    {
        return $this->params[$name] ?? $defaultValue;
    }
}