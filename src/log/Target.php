<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/7
 * Time: 21:23
 */

namespace kawaii\log;


use kawaii\base\Object;

abstract class Target extends Object
{
    public $enabled = true;
    public $messages = [];

    abstract public function export();
}