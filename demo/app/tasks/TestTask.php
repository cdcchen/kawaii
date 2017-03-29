<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/23
 * Time: 09:52
 */

namespace app\tasks;


use kawaii\base\BaseTask;

class TestTask extends BaseTask
{
    protected function onTasking($data)
    {
        echo "Receive a task: {$this->getTaskId()}, data: \n";
        var_dump($data);
        echo "\n";

        return 'OK';
    }

    protected function onDone($result)
    {
        echo "Task: {$this->getTaskId()} execute finished...\nData: $result \n";
    }
}