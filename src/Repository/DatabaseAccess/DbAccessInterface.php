<?php

namespace App\Repository\DatabaseAccess;

interface DbAccessInterface
{
    public function getAllTask(): array;

    public function delTask(int $id);

}