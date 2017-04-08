<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2017/4/8
 * Time: 23:56
 */

namespace kawaii\base;


/**
 * Interface ContextInterface
 * @package kawaii\base
 */
interface ContextInterface
{
    /**
     * @return ApplicationInterface
     */
    public function getApp(): ApplicationInterface;
}