<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2016/11/8
 * Time: 16:06
 */

namespace kawaii\caching;


use kawaii\base\Object;

class Cache extends Object
{
    public function connected()
    {
        return true;
    }

    public function getLineCount()
    {
        return 9;
    }

    public function hasCount()
    {
        return true;
    }

    public function count()
    {
        return 100;
    }
}