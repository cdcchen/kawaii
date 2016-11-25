<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2016/11/9
 * Time: 18:00
 */

namespace kawaii\util;


/**
 * Class CArray
 * @package kawaii\util
 */
class CArray implements \Countable, \ArrayAccess, \IteratorAggregate//, \Serializable, \JsonSerializable
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * CArray constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->container = $items;
    }

    public function sort($flags = SORT_REGULAR)
    {
        return sort($this->container, $flags);
    }

    public function reverseSort($flags = SORT_REGULAR)
    {
        return rsort($this->container, $flags);
    }

    public function keySort($flags = SORT_REGULAR)
    {
        return ksort($this->container, $flags);
    }

    public function keyReverseSort($flags = SORT_REGULAR)
    {
        return krsort($this->container, $flags);
    }

    public function assocSort($flags = SORT_REGULAR)
    {
        return asort($this->container, $flags);
    }

    public function assocReverseSort($flags = SORT_REGULAR)
    {
        return arsort($this->container, $flags);
    }

    public function userSort(callable $callback)
    {
        return usort($this->container, $callback);
    }

    public function userAssocSort(callable $callback)
    {
        return uasort($this->container, $callback);
    }

    public function userReverseSort(callable $callback)
    {
        return uksort($this->container, $callback);
    }

    public function natCaseSort()
    {
        return natcasesort($this->container);
    }

    public function natSort()
    {
        return natsort($this->container);
    }

    public function shuffle()
    {
        return shuffle($this->container);
    }

    public function keys()
    {
        return array_keys($this->container);
    }

    public function values()
    {
        return array_values($this->container);
    }

    public function changeKeyCase($case = CASE_LOWER)
    {
        return $this->container = array_change_key_case($this->container, $case);
    }

    public function chunk($size, $preserveKeys = false)
    {
        return array_chunk($this->container, $size, $preserveKeys);
    }

    public function column($columnKey, $indexKey = null)
    {
        if ($indexKey) {
            return array_column($this->container, $columnKey, $indexKey);
        } else {
            return array_column($this->container, $columnKey);
        }
    }

    public function countValues()
    {
        return array_count_values($this->container);
    }

    public function diff($array1, ...$array)
    {
        return array_diff($this->container, $array1, ...$array);
    }

    public function diffAssoc($array1, ...$array)
    {
        return array_diff_assoc($this->container, $array1, ...$array);
    }

    public function diffKey($array1, ...$array)
    {
        return array_diff_key($this->container, $array1, ...$array);
    }

    public function diffUserKey(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_diff_ukey($this->container, $array1, ...$array);
    }

    public function diffUserAssoc(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_diff_uassoc($this->container, $array1, ...$array);
    }

    public function userDiff($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_udiff($this->container, $array1, ...$array);
    }

    public function userDiffAssoc($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_udiff($this->container, $array1, ...$array);
    }

    public function userDiffUserAssoc($array1, callable $dataCompareCallback, callable $keyCompareCallback, ...$array)
    {
        $array[] = $dataCompareCallback;
        $array[] = $keyCompareCallback;
        return array_udiff($this->container, $array1, ...$array);
    }

    public function intersect($array1, ...$array)
    {
        return array_intersect($this->container, $array1, ...$array);
    }

    public function intersectAssoc($array1, ...$array)
    {
        return array_intersect_assoc($this->container, $array1, ...$array);
    }

    public function intersectKey($array1, ...$array)
    {
        return array_intersect_key($this->container, $array1, ...$array);
    }

    public function intersectUserKey(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_intersect_ukey($this->container, $array1, ...$array);
    }

    public function intersectUserAssoc(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_intersect_uassoc($this->container, $array1, ...$array);
    }

    public function fill($startIndex, $num, $value)
    {
        $this->container = array_fill($startIndex, $num, $value);
        return $this;
    }

    public function fillKeys($keys, $value)
    {
        $this->container = array_fill_keys($keys, $value);
        return $this;
    }

    public function flip()
    {
        $this->container = array_flip($this->container);
        return $this;
    }

    public function filter(callable $callback = null, $flag = 0)
    {
        if ($callback === null) {
            $this->container = array_filter($this->container);
        } else {
            $this->container = array_filter($this->container, $callback, $flag);
        }

        return $this;
    }

    public function keyExist($key)
    {
        return array_key_exists($key, $this->container);
    }

    public function map(callable $callback)
    {
        $this->container = array_map($callback, $this->container);
        return $this;
    }

    public function merge(...$array)
    {
        $this->container = array_merge($this->container, ...$array);
        return $this;
    }

    public function recursiveMerge(...$array)
    {
        $this->container = array_merge_recursive($this->container, ...$array);
        return $this;
    }

    public function pad($size, $value)
    {
        $this->container = array_pad($this->container, $size, $value);
        return $this;
    }

    public function pop()
    {
        return array_pop($this->container);
    }

    public function push($value1, ...$value)
    {
        return array_push($this->container, $value1, ...$value);
    }

    public function shift()
    {
        return array_shift($this->container);
    }

    public function unshift($value1, ...$value)
    {
        return array_unshift($this->container, $value1, ...$value);
    }

    public function product()
    {
        return array_product($this->container);
    }

    public function sum()
    {
        return array_sum($this->container);
    }

    public function rand($num = 1)
    {
        return array_rand($this->container, $num);
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->container, $callback, $initial);
    }

    public function replace($array1, ...$array)
    {
        $this->container = array_replace($this->container, $array1, ...$array);
        return $this;
    }

    public function recursiveReplace($array1, ...$array)
    {
        $this->container = array_replace_recursive($this->container, $array1, ...$array);
        return $this;
    }

    public function reverse($preserveKeys = false)
    {
        $this->container = array_reverse($this->container, $preserveKeys);
        return $this;
    }

    public function search($value, $strict = false)
    {
        return array_search($value, $this->container, $strict);
    }

    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return array_slice($this->container, $offset, $length, $preserveKeys);
    }

    public function splice($offset, $length = 0, $replacement = null)
    {
        return array_slice($this->container, $offset, $length, $replacement);
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
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->container);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->container);
    }
}