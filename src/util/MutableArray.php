<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/28
 * Time: 10:27
 */

namespace kawaii\util;


    /**
     * Class MutableArray
     * @package kawaii\base
     */
    /**
     * Class MutableArray
     * @package kawaii\base
     */
    /**
     * Class MutableArray
     * @package kawaii\util
     */
    /**
     * Class MutableArray
     * @package kawaii\util
     */
/**
 * Class MutableArray
 * @package kawaii\util
 */
class MutableArray extends ImmutableArray
{
    /**
     * @param mixed $value
     * @param int|string|bool $key
     * @return bool
     */
    public function push($value, $key = false)
    {
        if ($key === false) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * @param array|\Traversable $items
     */
    public function addAll($items)
    {
        $this->batchAdd($items);
    }

    /**
     * Remove all items
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function remove($value)
    {
        $keys = array_keys($this->items, $value, true);

        foreach ($keys as $key) {
            unset($this->items[$key]);
        }

        return true;
    }

    /**
     * @param null $items
     * @return bool
     */
    public function removeAll($items = null)
    {
        if (empty($items)) {
            $this->clear();
            return true;
        }

        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if ($items !== null && !is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        foreach ($items as $item) {
            $this->remove($item);
        }

        return true;
    }

    /**
     * @param int|string|array $keys
     */
    public function removeKeys($keys)
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnset($key);
        }
    }

    /**
     * @param array|\Traversable $items
     * @param bool $strict
     * @return bool
     */
    public function retainAll($items, $strict = false)
    {
        if (empty($this->items)) {
            return true;
        }

        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if (!is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }
        if (empty($items)) {
            throw new \InvalidArgumentException('At least one of the elements in retain items.');
        }

        $this->items = $strict ? array_intersect_assoc($items, $this->items) : array_intersect($items, $this->items);

        return true;
    }

    /**
     * @param int|string|array $keys
     * @return bool
     */
    public function retainKeys($keys)
    {
        if (empty($this->items)) {
            return true;
        }

        $keys = (array)$keys;
        if (empty($keys)) {
            throw new \InvalidArgumentException('At least one of the elements in retain keys.');
        }

        $retain = array_fill_keys($keys, null);
        $this->items = array_intersect_key($retain, $this->items);

        return true;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}