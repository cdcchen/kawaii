<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/8
 * Time: 21:19
 */

namespace kawaii\base;


interface Reflective
{
    /**
     * @return \ReflectionClass
     */
    public function getClass();

    /**
     * @return \ReflectionObject
     */
    public function getObject();
}