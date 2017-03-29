<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/8
 * Time: 15:47
 */

namespace kawaii\util;


/**
 * Class CSet
 * @package kawaii\util
 */
class KSet extends BaseList
{
    /**
     * @param mixed $value
     * @return $this
     */
    public function add($value)
    {
        if (!in_array($value, $this->container)) {
            $this->container[] = $value;
        }

        return $this;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!in_array($value, $this->container)) {
            $this->container[] = $value;
        }
    }
}