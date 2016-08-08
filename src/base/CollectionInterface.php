<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/28
 * Time: 10:17
 */

namespace kawaii\base;


/**
 * Interface CollectionInterface
 * @package kawaii\base
 */
/**
 * Interface CollectionInterface
 * @package kawaii\base
 */
interface CollectionInterface extends \Countable, \IteratorAggregate
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function add($value);

    /**
     * @param \Traversable|array $items
     * @return mixed
     */
    public function addAll($items);

    /**
     * Remove all items
     */
    public function clear();

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value);

    /**
     * @param \Traversable|array $items
     * @return bool
     */
    public function containsAll($items);

    /**
     * @param mixed $value
     * @return bool
     */
    public function equals($value);

    /**
     * @return string
     */
    public function hashCode();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param mixed $value
     * @return bool
     */
    public function remove($value);

    /**
     * @param null|\Traversable|array $items
     * @return bool
     */
    public function removeAll($items = null);

    /**
     * @param null|\Traversable|array $items
     * @return bool
     */
    public function retainAll($items = null);

    /**
     * @return int
     */
    public function size();

    /**
     * @return array
     */
    public function toArray();

}