<?php

namespace App\Entity\DTOEntity;

class TasksForEmail
{
    private string $email;
    /** @var string[] $tasksName */
    private array $tasksName;

    public static function createFromDbal(array $dbal): array
    {
        /** @var TasksForEmail[] $result */
        $result = [];

        foreach ($dbal as $item) {
            $email = $item['email'];
            $name = $item['name'];

            if (!array_key_exists($email, $result)) {
                $result[$email] = new TasksForEmail();
                $result[$email]->email = $email;
            }

            $result[$email]->tasksName[] = $name;
        }

        return $result;
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