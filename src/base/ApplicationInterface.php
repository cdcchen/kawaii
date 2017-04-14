<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 20:27
 */

namespace kawaii\base;


/**
 * Interface ApplicationInterface
 * @package kawaii\base
 */
interface ApplicationInterface
{
    public function prepare(): void;

    /**
     * @param string $config
     */
    public function run(string $config): void;
}