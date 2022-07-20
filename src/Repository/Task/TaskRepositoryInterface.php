<?php

namespace App\Repository\Task;

use App\Entity\TaskModel;

interface TaskRepositoryInterface
{
    public function add(TaskModel $taskModel): void;
    public function getAll(): array;
}