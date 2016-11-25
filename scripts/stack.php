<?php

/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/26
 * Time: 16:44
 */

$stack = new SplDoublyLinkedList();
$stack->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP);

$stack->push(1);
$stack->push(2);
$stack->push(3);
$stack->push(4);


foreach ($stack as $item) {
    var_dump($item);
}