<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/29
 * Time: 16:58
 */

namespace kawaii\util;


    /**
     * Class HashArray
     * @package kawaii\base
     */
    /**
     * Class HashArray
     * @package kawaii\base
     */
/**
 * Class HashArray
 * @package kawaii\base
 */
class HashArray implements HashArrayInterface, \IteratorAggregate, \ArrayAccess, \Serializable, \JsonSerializable
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * HashArray constructor.
     * @param \Traversable|array $items
     */
    public function __construct($items)
    {
        $this->putAll($items);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * @inheritdoc
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @inheritdoc
     */
    public function containsValue($value)
    {
        return in_array($value, $this->items, true);
    }

    /**
     * @inheritdoc
     */
    public function equals($map)
    {
        return $this === $map;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->containsKey($key) ? $this->items[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @inheritdoc
     */
    public function keys()
    {
        return array_keys($this->items);
    }

    /**
     * @inheritdoc
     */
    public function put($key, $value)
    {
        // @todo 验证key的合法性
        if (0) {
            throw new \InvalidArgumentException('Invalid the key');
        }

        $previousValue = $this->get($key);
        $this->items[$key] = $value;
        return $previousValue;
    }

    /**
     * @inheritdoc
     */
    public function putAll($items)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if (is_array($items)) {
            $this->items = array_merge($this->items, $items);
        } else {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        if ($this->containsKey($key)) {
            $value = $this->items[$key];
            unset($this->items[$key]);
            return $value;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @inheritdoc
     */
    public function values()
    {
        return array_values($this->items);
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        return $this->put($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $this->items = $this->unserialize($serialized);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return json_encode($this->items, 320);
    }

    public function key()
    {
        return key($this->items);
    }

    public function current()
    {
        return current($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function pre()
    {
        return prev($this->items);
    }

    public function first()
    {
        return reset($this->items);
    }

    public function end()
    {
        return end($this->items);
    }

    public function each()
    {
        return each($this->items);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return $this->items;
    }
}