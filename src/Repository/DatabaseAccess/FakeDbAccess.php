<?php

namespace App\Repository\DatabaseAccess;

use App\Entity\Exception\WrongDateException;
use App\Entity\TaskJsonModel;
use App\Entity\TaskModel;
use App\Utils\PriorityTask;


class FakeDbAccess implements DbAccessInterface
{
    /** @var TaskModel[] */
    private array $tasks;


    /**
     * @throws WrongDateException
     * @throws \Exception
     */
    public function __construct(
        protected string $path
    )
    {
        $this->tasks = [];
        foreach (json_decode($this->readAllFile()) as $ele) {
            $this->tasks[] = new TaskModel(
                id: $ele->id,
                name: $ele->name,
                creatDate: new \DateTimeImmutable(datetime: $ele->creatDate->date),
//                priority: PriorityTask::fromInt($ele->priority),
                priority: PriorityTask::tryFrom($ele->priority),
            );
            if ($ele->endDate->date ?? false) {
                end($this->tasks)->setEndDate(new \DateTime(datetime: $ele->endDate->date));
            }
        }
        $this->save();
    }

    private
    function readAllFile(): string
    {
        $file = fopen($this->path, "r");
        $content = fread($file, filesize($this->path));
        fclose($file);
        return $content;
    }

    public
    function save(): void
    {

        $this->tasks = array_values($this->tasks);
        $tasksTemp = [];
        foreach ($this->tasks as $key => $task) {
            $tasksTemp[$key] = new TaskJsonModel($task);
        }
        file_put_contents($this->path, json_encode($tasksTemp));
    }

    private
    function findIndexTaskById(int $id): int
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

    public
    function getAllTask(): array
    {
        return $this->tasks;
    }

    /**
     * @throws \Exception
     */
    public
    function deleteTask(int $id): void
    {
        unset(
            $this->tasks[$this->findIndexTaskById($id)]
        );
        $this->save();
    }


    public
    function addTask(TaskModel $taskModel): void
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
     * @throws WrongDateException
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