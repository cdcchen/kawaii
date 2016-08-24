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
    protected function onTask($data)
    {
        echo "Receive a task...\n";
        $this->setSuccess(true);

        return 'OK';
    }

    protected function onSuccess($result)
    {
        echo "Task execute success...\nData: $result \n";
    }

    protected function onFailed($result)
    {
        echo "Task execute failed...\nData: $result \n";
    }

    protected function onFinished($result)
    {
        echo "Task execute finished...\nData: $result \n";
    }
}