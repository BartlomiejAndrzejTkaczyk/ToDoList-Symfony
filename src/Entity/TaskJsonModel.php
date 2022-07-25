<?php

namespace App\Entity;

use App\Utils\PriorityTask;
use DateTime;

class TaskJsonModel
{
    public int $id;
    public string $name;
    public ?DateTime $creatDate;
    public ?DateTime $endDate = null;
    public PriorityTask $priority;

    public function __construct(TaskModel $taskModel)
    {
        $this->id = $taskModel->getId();
        $this->name = $taskModel->getName();
        $this->creatDate = $taskModel->getCreatDate();
        $this->endDate = $taskModel->getEndDate();
        $this->priority = $taskModel->getPriority();
    }

    public function convertToTaskModel(): TaskModel
    {
        return new TaskModel(
            id: $this->id,
            name: $this->name,
            creatDate: $this->creatDate,
            endDate: $this->endDate,
            priority: $this->priority,
        );
    }
}