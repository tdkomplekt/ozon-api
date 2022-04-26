<?php

namespace Tdkomplekt\OzonApi\Helpers;

use Google\Service\Tasks\Task;
use Tdkomplekt\OzonApi\Jobs\OzonCheckTaskResultJob;
use Tdkomplekt\OzonApi\Models\OzonTask;

class OzonHelper
{

    public static function saveTaskFromResponse($ozonApiResponse)
    {
        if ($ozonApiResponse) {
            $taskId = OzonHelper::getTaskIdFromResponse($ozonApiResponse);
            if($taskId) {
                $task = OzonTask::firstOrCreate([
                    'id' =>  $taskId
                ]);

                // todo run the task checking job
            }
        }
    }

    public static function getTaskIdFromResponse($ozonApiResponse)
    {
        $data = json_decode($ozonApiResponse, true);
        return isset($data['result']) && isset($data['result']['task_id']) ? $data['result']['task_id'] : null ;
    }

    public static function getTaskIdFromResponseAndSaveTask($ozonApiResponse)
    {
        $taskId = self::getTaskIdFromResponse($ozonApiResponse);
        if($taskId) {
            self::saveTaskFromResponse($ozonApiResponse);
        }

        return $taskId;
    }

    public static function checkTasksInProcessing($dispatchNow = false)
    {
        $tasks = OzonTask::whereNull('check_result')->get();

        foreach ($tasks as $task) {
            $job = new OzonCheckTaskResultJob($task);

            if ($dispatchNow) {
                $job->handle();
            } else {
                dispatch($job);
            }
        }
    }
}
