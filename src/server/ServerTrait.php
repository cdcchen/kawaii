<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/13
 * Time: 18:02
 */

namespace kawaii\server;


trait ServerTrait
{
    /**
     * @param string $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getSetting(string $name, $defaultValue = null)
    {
        return $this->setting[$name] ?? $defaultValue;
    }
}