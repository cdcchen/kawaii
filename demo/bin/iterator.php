<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/9/8
 * Time: 17:09
 */

$a = [1, 2];
$b = [3, 4];
$c = [5, 6];

$iterator = new AppendIterator();
$iterator->append(new ArrayIterator($a));
$iterator->append($b);
$iterator->append($c);

foreach ($iterator as $item) {
    var_dump($item);
}