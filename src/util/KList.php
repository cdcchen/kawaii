<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/8
 * Time: 15:47
 */

namespace kawaii\util;


/**
 * Class CList
 * @package kawaii\util
 */
class KList extends BaseList
{
    /**
     * @param mixed $value
     * @return $this
     */
    public function add($value)
    {
        $this->container[] = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @param bool $strict
     * @return $this
     */
    public function removeAll($value, $strict = false)
    {
        while (($index = array_search($value, $this->container, $strict)) !== false) {
            array_splice($this->container, $index, 1);
        }

        return $this;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->container[] = $value;
    }
}