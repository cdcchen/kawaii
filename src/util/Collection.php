<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/28
 * Time: 10:27
 */

namespace kawaii\util;


    /**
     * Class Collection
     * @package kawaii\base
     */
/**
 * Class Collection
 * @package kawaii\base
 */
class Collection implements CollectionInterface
{
    /**
     * @var array
     */
    private $items = [];

    public function __construct($items = [])
    {
        $this->addAll($items);
    }

    /**
     * @inheritdoc
     */
    public function add($value)
    {
        $this->items[] = $value;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function addAll($items)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if (!is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        foreach ($items as $item) {
            $this->items[] = $item;
        }

        return true;
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
    public function contains($value)
    {
        return in_array($value, $this->items, true);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function equals($value)
    {
        return $this === $value;
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
    public function remove($value)
    {
        $keys = array_keys($this->items, $value, true);

        foreach ($keys as $key) {
            unset($this->items[$key]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeAll($items = null)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if ($items !== null && !is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        if (empty($items)) {
            $this->clear();
            return true;
        }

        foreach ($items as $item) {
            $this->remove($item);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function retainAll($items = null)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        if ($items !== null && !is_array($items)) {
            throw new \InvalidArgumentException('Argument is must be an array or an instance of Traversable.');
        }

        if (empty($items)) {
            return true;
        }

        foreach ($this->items as $index => $item) {
            if (!in_array($item, $items)) {
                unset($this->items[$index]);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function size()
    {
        $this->count();
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
    public function count()
    {
        return count($this->items);
    }
}