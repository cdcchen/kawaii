<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/8
 * Time: 16:34
 */

namespace kawaii\util;


/**
 * Class ImmutableArray
 * @package kawaii\util
 */
class ImmutableArray implements \ArrayAccess, \Countable, \IteratorAggregate, \Serializable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * ImmutableArray constructor.
     * @param array|\Traversable $items
     */
    public function __construct($items = [])
    {
        $this->batchAdd($items);
    }

    /**
     * @param array|\Traversable $items
     */
    protected function batchAdd($items)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if (!is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        foreach ($items as $key => $item) {
            $this->items[$key] = $item;
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value)
    {
        return in_array($value, $this->items, true);
    }


    /**
     * @param array|\Traversable $items
     * @return bool
     */
    public function containsAll($items)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if (!is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        foreach ($items as $item) {
            if (!$this->contains($item)) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param static $value
     * @return bool
     */
    public function equals($value)
    {
        return $this === $value;
    }


    /**
     * @return string
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    public function keys()
    {
        return array_keys($this->items);
    }

    public function values()
    {
        return array_values($this->items);
    }


    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }


    /**
     * @return array
     */
    public function getIterator()
    {
        return $this->items;
    }


    /**
     * @param int $mode value is COUNT_NORMAL | COUNT_RECURSIVE
     * @return int
     */
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->items, $mode);
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->items = $this->unserialize($serialized);
    }

    /**
     * @return string
     */
    function jsonSerialize()
    {
        return json_encode($this->items, 320);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('This is a immutable array.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('This is a immutable array.');
    }
}