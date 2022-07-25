<?php

namespace App\Repository\DatabaseAccess;

use App\Entity\TaskModel;

interface DbAccessInterface
{
    public function getAllTask(): array;

    public function deleteTask(int $id);

    public function addTask(TaskModel $taskModel);

    public function save();

    public function createTaskTemplate(): TaskModel;

}