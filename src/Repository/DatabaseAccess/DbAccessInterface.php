<?php

namespace App\Repository\DatabaseAccess;

interface DbAccessInterface
{
    public function getAllTask(): array;

    public function deleteTask(int $id);

}