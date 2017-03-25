<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/8
 * Time: 15:47
 */

namespace kawaii\util;


/**
 * Class CBaseList
 * @package kawaii\util
 */
abstract class BaseList implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
    protected $container = [];

    /**
     * @param mixed $value
     * @return $this
     */
    abstract public function add($value);

    /**
     * @param mixed $value
     * @param bool $strict
     * @return $this
     */
    public function remove($value, $strict = false)
    {
        $index = array_search($value, $this->container, $strict);
        if ($index !== false) {
            array_splice($this->container, $index, 1);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->container);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->container = [];
        return $this;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function contains($value)
    {
        return in_array($value, $this->container);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->container);
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->container);
    }


    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        array_splice($this->container, $offset, 1);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->container;
    }
}