<?php

namespace App\Repository\DatabaseAccess;

use App\Entity\TaskJsonModel;
use App\Entity\TaskModel;
use App\Utils\PriorityTask;


class FakeDbAccess implements DbAccessInterface
{
    const PATH = 'C:\Users\Piter\OneDrive\Dokumenty\Bartek\staz\ToDoList-Symfony\src\Res\Tasks.json';
    private array $tasks;


    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function __construct()
    {
        $this->tasks = [];
        foreach (json_decode($this->readAllFile()) as $ele) {
            $this->tasks[] = new TaskModel(
                id: $ele->id,
                name: $ele->name,
                creatDate: new \DateTime(datetime: $ele->creatDate->date),
                priority: PriorityTask::fromInt($ele->priority),
            );
            if ($ele->endDate->date ?? false) {
                end($this->tasks)->setEndDate(new \DateTime(datetime: $ele->endDate->date));
            }
        }
        $this->save();
    }

    private function readAllFile(): string
    {
        $file = fopen(self::PATH, "r");
        $content = fread($file, filesize(self::PATH));
        fclose($file);
        return $content;
    }

    public function save(): void
    {
        if (count($this->tasks) == 0) {
            return;
        }
        $this->tasks = array_values($this->tasks);
        $tasksTemp = [];
        foreach ($this->tasks as $key => $task) {
            $tasksTemp[$key] = new TaskJsonModel($task);
        }
        file_put_contents(self::PATH, json_encode($tasksTemp));
    }

    private function findIndexTaskById(int $id): int
    {
        /**
         * @var int $key
         * @var TaskModel $task
         */
        foreach ($this->tasks as $key => $task) {
            if ($task->getId() == $id) {
                return $key;
            }
        }
        throw new \Exception("No task with $id");
    }

    public function getAllTask(): array
    {
        return $this->tasks;
    }

    /**
     * @throws \Exception
     */
    public function deleteTask(int $id): void
    {
        unset(
            $this->tasks[$this->findIndexTaskById($id)]
        );
        $this->save();
    }


    public function addTask(TaskModel $taskModel): void
    {
        $this->tasks[] = $taskModel;
        $this->save();
    }

    /**
     * @throws \Exception
     */
    public function editTask(int $id, string $name): int
    {
        $index = $this->findIndexTaskById($id);

        /** @var TaskModel $task */
        $task = $this->tasks[$index];

        $task->setName($name);

        $this->save();

        return 200;
    }

    /**
     * @throws \Exception
     */
    public function getTaskById(int $id): TaskModel
    {
        return $this->tasks[$this->findIndexTaskById($id)];
    }

    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function createTaskTemplate(): TaskModel
    {
        $max = 0;
        /** @var TaskModel $task */
        foreach ($this->tasks as $task) {
            if ($max < $task->getId()) {
                $max = $task->getId();
            }
        }
        return new TaskModel($max, '', new \DateTime());
    }

}