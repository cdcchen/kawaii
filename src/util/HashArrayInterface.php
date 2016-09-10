<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/29
 * Time: 16:53
 */

namespace kawaii\util;


/**
 * Interface HashArrayInterface
 * @package kawaii\base
 */
interface HashArrayInterface extends \Countable
{
    /**
     * Remove all
     */
    public function clear();

    /**
     * @param string $key
     * @return bool
     */
    public function containsKey($key);

    /**
     * @param mixed $value
     * @return bool
     */
    public function containsValue($value);

    /**
     * @param HashArrayInterface $map
     * @return bool
     */
    public function equals($map);

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @return string
     */
    public function hashCode();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return array
     */
    public function keys();

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value);

    /**
     * @param \Traversable|array $items
     */
    public function putAll($items);

    /**
     * @param string $key
     * @return mixed
     */
    public function remove($key);

    /**
     * @return array
     */
    public function values();

    /**
     * @return array
     */
    public function toArray();
}