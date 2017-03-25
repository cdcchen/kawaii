<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2016/11/9
 * Time: 14:05
 */

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../vendor/autoload.php';


$list = new \kawaii\util\KList();

$list[] = 'xx';
$list[] = 'yy';
$list[] = 111;
$list[] = 'zz';
$list[] = time();
$list[] = 1111;
unset($list[2]);
$list[] = 'zz';
$list[] = '111';
$list[] = 111;
$list[] = 'zz';
$list[] = time();
$list[] = 111;

print_r($list->toArray());

$list->add(__FILE__);
//var_dump($list->contains(111));
$list->removeAll('111');

print_r($list->toArray());