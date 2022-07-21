<?php

namespace App\Repository\DatabaseAccess;

use App\Entity\TaskModel;
use App\Utils\PriorityTask;
use Psr\Log\LoggerInterface;

class FakeDbAccess implements DbAccessInterface
{
    const PATH = '/var/www/html/ToDoList/src/Res/Tasks.json';
    private array $tasks;


    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function __construct(
        private LoggerInterface $logger
    )
    {
        $this->tasks = json_decode($this->readAllFile(self::PATH));
    }

    private function readAllFile(string $path): string
    {
        $file = fopen($path, "r");
        $content = fread($file, filesize($path));
        fclose($file);
        return $content;
    }

    private function save(): void
    {
        $this->tasks = array_values($this->tasks);
        file_put_contents(self::PATH, json_encode($this->tasks));
    }

    private function findIndexTaskById(int $id): int
    {
        /**
         * @var int $key
         * @var TaskModel $task
         */
        foreach ($this->tasks as $key => $task) {
            if ($task->id == $id) {
                return $key;
            }
        }
        throw new \Exception("No task with $id");
    }

    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function getAllTask(): array
    {
        return $this->tasks;
    }

    /**
     * @throws \Exception
     */
    public function delTask(int $id): void
    {
        unset(
            $this->tasks[$this->findIndexTaskById($id)]
        );
        $this->save();
    }

    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    public function addTask(TaskModel $taskModel): int
    {
        $this->tasks[] = $taskModel;
        $this->logger->info("Add task $taskModel->name with id=$taskModel->id");
        $this->save();
        return 200;
    }

    public function editTask(int $id, string $name): int
    {
        $index = $this->findIndexTaskById($id);

        /** @var TaskModel $task */
        $task = $this->tasks[$index];

        $task->name = $name;

        $this->save();

        return 200;
    }
}