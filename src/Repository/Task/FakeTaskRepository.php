<?php

namespace App\Repository\Task;

use App\Entity\TaskModel;
use App\Utils\PriorityTask;

class FakeTaskRepository implements TaskRepositoryInterface
{

    public function add(TaskModel $taskModel): void
    {
        // TODO: Implement add() method.
    }

    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function getAll(): array
    {
        return [
            new TaskModel('Make dinner', priority: PriorityTask::Medium), // todo make or cook?
            new TaskModel('Roch Birthday'),
            new TaskModel('Go to gym',)
        ];
    }
}