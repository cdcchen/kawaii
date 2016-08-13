<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/8
 * Time: 21:09
 */

namespace kawaii\util;


class HashSet extends Collection
{
    private static function getItemHash($item)
    {
        if (is_scalar($item)) {
            return md5(gettype($item) . $item);
        }
        if (is_object($item)) {
            return spl_object_hash($item);
        }
    }
}