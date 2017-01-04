<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/8
 * Time: 21:19
 */

namespace kawaii\base;


/**
 * Interface Reflective
 * @package kawaii\base
 */
interface Reflective
{
    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass();

    /**
     * @return \ReflectionObject
     */
    public function getReflectionObject();
}