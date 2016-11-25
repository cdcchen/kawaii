<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 2016/11/9
 * Time: 14:05
 */

date_default_timezone_set('Asia/Shanghai');
require __DIR__ . '/../vendor/autoload.php';


$list = new \kawaii\util\CArray();

$list[] = 'xx';
$list[] = 'yy';
$list[] = 'yy';
$list[] = 'zz';
$list['xxx'] = 'xxxx';
$list[] = time();
$list[] = 1111;
$list[] = '';
$list[] = '';
$list[] = 'wefwefwe';
//unset($list[2]);

//echo "\n\n";
//foreach ($list as $key => $item) {
//    echo "$key => $item\n";
//}


//print_r($list);
//$list->flip();
//print_r($list);
//
//$b = $list->slice(1, 3);
//print_r($b);
//$c = array_splice($b, 1,2,'x');
//print_r($b);

//$list->splice(10);
print_r($list->toArray());
$list->insert(1, __FILE__);
print_r($list->toArray());
var_dump($list->valueCount('yyxxxxx'));

echo "\n======================\n";
//$stack = new SplMaxHeap();
//$stack->insert(111);
//$stack->insert(222);
//$stack->insert(222);
//$stack->insert(333);
//print_r(iterator_to_array($stack));

$set = new \kawaii\util\CSet();
$set->insert(1111);
$set->insert(2222);
$set->insert(3333);
$set->insert(3333);
$set->insert(4444);
//print_r(iterator_to_array($set));
var_dump($set->extract());
var_dump($set->extract());
var_dump($set->extract());
var_dump($set->extract());
var_dump($set->extract());


