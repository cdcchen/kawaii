<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/15
 * Time: 13:10
 */

$func = function () {

};

function test()
{

}

class A
{
    function test()
    {

    }
}

class B
{
    static function test()
    {

    }
}


$n = 10000000;

$start = microtime(true);
for ($i=0; $i<$n; $i++) {
//    $func();
    call_user_func($func);
}
$time = microtime(true) - $start;
echo "Time: {$time}\n\n";


$start = microtime(true);
for ($i=0; $i<$n; $i++) {
//    test();
    call_user_func('test');
}
$time = microtime(true) - $start;
echo "Time: {$time}\n\n";

$start = microtime(true);
for ($i=0; $i<$n; $i++) {
//    B::test();
    call_user_func(['B', 'test']);
}
$time = microtime(true) - $start;
echo "Time: {$time}\n\n";


$start = microtime(true);
$a = new A();
for ($i=0; $i<$n; $i++) {
//    $a->test();
    call_user_func([$a, 'test']);
}
$time = microtime(true) - $start;
echo "Time: {$time}\n\n";