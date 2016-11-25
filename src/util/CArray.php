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
/**
 * Class CArray
 * @package kawaii\util
 */
class CArray implements \Countable, \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $container = [];

    /**
     * CArray constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->container = $items;
    }


    /**
     * @param mixed $value
     * @param null|int|string $key
     * @return $this
     */
    public function add($value, $key = null)
    {
        if ($key === null) {
            $this->container[] = $value;
        } else {
            $this->container[$key] = $value;
        }

        return $this;
    }

    /**
     * @param int $position
     * @param mixed $value
     * @return $this
     */
    public function insert($position, $value)
    {
        array_splice($this->container, $position, 0, $value);
        return $this;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return $this
     */
    public function remove($value, $strict = false)
    {
        $key = array_search($value, $this->container, $strict);
        if ($key !== false) {
            unset($this->container[$key]);
        }

        return $this;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return $this
     */
    public function removeAll($value, $strict = false)
    {
        while ($key = array_search($value, $this->container, $strict) !== false) {
            unset($this->container[$key]);
        }

        return $this;
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
     * @param $value
     * @param bool $strict
     * @return bool
     */
    public function contains($value, $strict = false)
    {
        return in_array($value, $this->container, $strict);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->container);
    }

    /**
     * @param $array
     * @return $this
     */
    public function extend($array)
    {
        if ($array instanceof self) {
            $array = $array->toArray();
        }
        if (!is_array($array)) {
            throw new \InvalidArgumentException('extend(x): x not a array');
        }

        $this->container = array_merge($this->container, $array);
        return $this;
    }


    /**
     * @param int $flags
     * @return bool
     */
    public function sort($flags = SORT_REGULAR)
    {
        return sort($this->container, $flags);
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function reverseSort($flags = SORT_REGULAR)
    {
        return rsort($this->container, $flags);
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function keySort($flags = SORT_REGULAR)
    {
        return ksort($this->container, $flags);
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function keyReverseSort($flags = SORT_REGULAR)
    {
        return krsort($this->container, $flags);
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function assocSort($flags = SORT_REGULAR)
    {
        return asort($this->container, $flags);
    }

    /**
     * @param int $flags
     * @return bool
     */
    public function assocReverseSort($flags = SORT_REGULAR)
    {
        return arsort($this->container, $flags);
    }

    /**
     * @param callable $callback
     * @return bool
     */
    public function userSort(callable $callback)
    {
        return usort($this->container, $callback);
    }

    /**
     * @param callable $callback
     * @return bool
     */
    public function userAssocSort(callable $callback)
    {
        return uasort($this->container, $callback);
    }

    /**
     * @param callable $callback
     * @return bool
     */
    public function userReverseSort(callable $callback)
    {
        return uksort($this->container, $callback);
    }

    /**
     * @return bool
     */
    public function natCaseSort()
    {
        return natcasesort($this->container);
    }

    /**
     * @return bool
     */
    public function natSort()
    {
        return natsort($this->container);
    }

    /**
     * @return bool
     */
    public function shuffle()
    {
        return shuffle($this->container);
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->container);
    }

    /**
     * @return array
     */
    public function values()
    {
        return array_values($this->container);
    }

    /**
     * @param int $case
     * @return array
     */
    public function changeKeyCase($case = CASE_LOWER)
    {
        return $this->container = array_change_key_case($this->container, $case);
    }

    /**
     * @param $size
     * @param bool $preserveKeys
     * @return array
     */
    public function chunk($size, $preserveKeys = false)
    {
        return array_chunk($this->container, $size, $preserveKeys);
    }

    /**
     * @param $columnKey
     * @param null $indexKey
     * @return array
     */
    public function column($columnKey, $indexKey = null)
    {
        if ($indexKey) {
            return array_column($this->container, $columnKey, $indexKey);
        } else {
            return array_column($this->container, $columnKey);
        }
    }

    /**
     * @return array
     */
    public function valuesCount()
    {
        return array_count_values($this->container);
    }

    /**
     * @param $value
     * @return int
     */
    public function valueCount($value)
    {
        $counters = array_count_values($this->container);
        return isset($counters[$value]) ? $counters[$value] : 0;
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function diff($array1, ...$array)
    {
        return array_diff($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function diffAssoc($array1, ...$array)
    {
        return array_diff_assoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function diffKey($array1, ...$array)
    {
        return array_diff_key($this->container, $array1, ...$array);
    }

    /**
     * @param callable $callback
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function diffUserKey(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_diff_ukey($this->container, $array1, ...$array);
    }

    /**
     * @param callable $callback
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function diffUserAssoc(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_diff_uassoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $callback
     * @param array ...$array
     * @return array
     */
    public function userDiff($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_udiff($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $callback
     * @param array ...$array
     * @return array
     */
    public function userDiffAssoc($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_udiff_assoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $dataCompareCallback
     * @param callable $keyCompareCallback
     * @param array ...$array
     * @return array
     */
    public function userDiffUserAssoc($array1, callable $dataCompareCallback, callable $keyCompareCallback, ...$array)
    {
        $array[] = $dataCompareCallback;
        $array[] = $keyCompareCallback;
        return array_udiff_uassoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function intersect($array1, ...$array)
    {
        return array_intersect($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function intersectAssoc($array1, ...$array)
    {
        return array_intersect_assoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function intersectKey($array1, ...$array)
    {
        return array_intersect_key($this->container, $array1, ...$array);
    }

    /**
     * @param callable $callback
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function intersectUserKey(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_intersect_ukey($this->container, $array1, ...$array);
    }

    /**
     * @param callable $callback
     * @param $array1
     * @param array ...$array
     * @return array
     */
    public function intersectUserAssoc(callable $callback, $array1, ...$array)
    {
        $array[] = $callback;
        return array_intersect_uassoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $callback
     * @param array ...$array
     * @return array
     */
    public function userIntersect($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_uintersect($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $callback
     * @param array ...$array
     * @return array
     */
    public function userIntersectAssoc($array1, callable $callback, ...$array)
    {
        $array[] = $callback;
        return array_uintersect_assoc($this->container, $array1, ...$array);
    }

    /**
     * @param $array1
     * @param callable $dataCompareCallback
     * @param callable $keyCompareCallback
     * @param array ...$array
     * @return array
     */
    public function userIntersectUserAssoc(
        $array1,
        callable $dataCompareCallback,
        callable $keyCompareCallback,
        ...$array
    ) {
        $array[] = $dataCompareCallback;
        $array[] = $keyCompareCallback;
        return array_uintersect_uassoc($this->container, $array1, ...$array);
    }

    /**
     * @param $startIndex
     * @param $num
     * @param $value
     * @return $this
     */
    public function fill($startIndex, $num, $value)
    {
        $this->container = array_fill($startIndex, $num, $value);
        return $this;
    }

    /**
     * @param $keys
     * @param $value
     * @return $this
     */
    public function fillKeys($keys, $value)
    {
        $this->container = array_fill_keys($keys, $value);
        return $this;
    }

    /**
     * @return $this
     */
    public function flip()
    {
        $this->container = array_flip($this->container);
        return $this;
    }

    /**
     * @param callable|null $callback
     * @param int $flag
     * @return $this
     */
    public function filter(callable $callback = null, $flag = 0)
    {
        if ($callback === null) {
            $this->container = array_filter($this->container);
        } else {
            $this->container = array_filter($this->container, $callback, $flag);
        }

        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function keyExist($key)
    {
        return array_key_exists($key, $this->container);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function map(callable $callback)
    {
        $this->container = array_map($callback, $this->container);
        return $this;
    }

    /**
     * @param callable $callback
     * @param null $userData
     * @return $this
     */
    public function walk(callable $callback, $userData = null)
    {
        array_walk($this->container, $callback, $userData);
        return $this;
    }

    /**
     * @param array ...$array
     * @return $this
     */
    public function merge(...$array)
    {
        $this->container = array_merge($this->container, ...$array);
        return $this;
    }

    /**
     * @param array ...$array
     * @return $this
     */
    public function recursiveMerge(...$array)
    {
        $this->container = array_merge_recursive($this->container, ...$array);
        return $this;
    }

    /**
     * @param $size
     * @param $value
     * @return $this
     */
    public function pad($size, $value)
    {
        $this->container = array_pad($this->container, $size, $value);
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
     * @param $value1
     * @param array ...$value
     * @return int
     */
    public function push($value1, ...$value)
    {
        return array_push($this->container, $value1, ...$value);
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->container);
    }

    /**
     * @param $value1
     * @param array ...$value
     * @return int
     */
    public function unshift($value1, ...$value)
    {
        return array_unshift($this->container, $value1, ...$value);
    }

    /**
     * @return number
     */
    public function product()
    {
        return array_product($this->container);
    }

    /**
     * @return number
     */
    public function sum()
    {
        return array_sum($this->container);
    }

    /**
     * @param int $num
     * @return mixed
     */
    public function rand($num = 1)
    {
        return array_rand($this->container, $num);
    }

    /**
     * @param callable $callback
     * @param null $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->container, $callback, $initial);
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return $this
     */
    public function replace($array1, ...$array)
    {
        $this->container = array_replace($this->container, $array1, ...$array);
        return $this;
    }

    /**
     * @param $array1
     * @param array ...$array
     * @return $this
     */
    public function recursiveReplace($array1, ...$array)
    {
        $this->container = array_replace_recursive($this->container, $array1, ...$array);
        return $this;
    }

    /**
     * @param bool $preserveKeys
     * @return $this
     */
    public function reverse($preserveKeys = false)
    {
        $this->container = array_reverse($this->container, $preserveKeys);
        return $this;
    }

    /**
     * @param $value
     * @param bool $strict
     * @return mixed
     */
    public function search($value, $strict = false)
    {
        return array_search($value, $this->container, $strict);
    }

    /**
     * @param $offset
     * @param null $length
     * @param bool $preserveKeys
     * @return array
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return array_slice($this->container, $offset, $length, $preserveKeys);
    }

    /**
     * @param $offset
     * @param int $length
     * @param null $replacement
     * @return array
     */
    public function splice($offset, $length = 0, $replacement = null)
    {
        return array_slice($this->container, $offset, $length, $replacement);
    }

    /**
     * @param int $flag
     * @return $this
     */
    public function unique($flag = SORT_STRING)
    {
        $this->container = array_unique($this->container, $flag);
        return $this;
    }


    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * @return mixed
     */
    public function prev()
    {
        return prev($this->container);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return next($this->container);
    }

    /**
     * @return mixed
     */
    public function end()
    {
        return end($this->container);
    }

    /**
     * @return mixed
     */
    public function reset()
    {
        return reset($this->container);
    }

    /**
     * @return array
     */
    public function each()
    {
        return each($this->container);
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

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->container;
    }
}