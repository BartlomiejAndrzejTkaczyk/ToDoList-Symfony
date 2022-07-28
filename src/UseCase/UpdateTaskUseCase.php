<?php

namespace App\UseCase;

use App\Entity\DTOEntity\TaskChange;
use App\Repository\TaskRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateTaskUseCase
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    )
    {
    }

    public function execute(TaskChange $newTask): void
    {
        $oldTask = $this->taskRepository->find($newTask->getId());

        if (!$oldTask) {
            throw new NotFoundHttpException('Not found task with id = ' . $newTask->getId());
        }

        if ($newTask->getEndDate()) {
            $oldTask->setEndDate($newTask->getEndDate());
        }

        if ($newTask->getEndDate())
            $oldTask->setEndDate($newTask->getEndDate());
        if ($newTask->getName() != null)
            $oldTask->setName($newTask->getName());
        if ($newTask->isFinish())
            $oldTask->setIsFinish($newTask->isFinish());


        $this->taskRepository->flush();
    }
}