<?php

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/24
 * Time: 13:49
 */

function test()
{
    for ($i=0; $i<5; $i++) {
        yield $i;
        echo $i . '----------' . PHP_EOL;
    }
}

$y = test();
var_dump($y->current());
$y->next();
var_dump($y->current());
$y->next();
var_dump($y->current());
$y->next();
var_dump($y->current());
$y->next();
var_dump($y->current());
$y->next();
var_dump($y->current());