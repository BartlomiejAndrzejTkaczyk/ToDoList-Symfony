<?php

namespace App\Entity\DTOEntity;

class TasksForEmail
{
    private string $email;
    /** @var string[] $tasksName */
    private array $tasksName;

    public static function createFromDbal($dbal): array
    {
        /** @var TasksForEmail[] $res */
        $res = [];

        foreach ($dbal as $item) {
            $key = $item['email'];
            $value = $item['name'];

            if (!array_key_exists($key, $res)) {
                $res[$key] = new TasksForEmail();
                $res[$key]->email = $key;
            }

            $res[$key]->tasksName[] = $value;
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string[]
     */
    public function getTasksName(): array
    {
        return $this->tasksName;
    }

    /**
     * @param string[] $tasksName
     */
    public function setTasksName(array $tasksName): void
    {
        $this->tasksName = $tasksName;
    }
}