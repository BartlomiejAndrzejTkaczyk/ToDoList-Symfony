<?php

namespace App\Utils;

use App\Entity\DTOEntity\TaskChange;

class FilterArray
{
    public static function isActive(TaskChange $task): bool
    {
        return (
            ($task->getEndDate() == null || $task->getEndDate() > new \DateTimeImmutable())
            && !$task->isFinish()
        );
    }

    public static function isFinish(TaskChange $task): bool
    {
        return $task->isFinish();
    }
}