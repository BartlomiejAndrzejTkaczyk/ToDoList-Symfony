<?php

namespace App\Entity\DTOEntity;

use App\Entity\DatabaseEntity\Task;

class TaskChange
{
    private int $id;
    private string $name;
    private \DateTimeImmutable $endDate;

    private int $userId;
    private string $email;

    /**
     * @throws \Exception
     */
    public static function createFromDbal(array $dbal): self
    {
        $task = new self();

        $task->setId($dbal['id']);
        $task->setName($dbal['name']);
        $task->setEndDate(new \DateTimeImmutable( $dbal['end_date']) );
        $task->setUserId($dbal['user_id'] );
        return $task;
    }


    public function castToTask()
    {
        $task = new Task();


        $this->name ?? $task->setName($this->name);
        $this->endDate ?? $task->setEndDate($this->endDate);
        $this->userId ?? $task->setUserId($this->userId);

    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeImmutable $endDate
     */
    public function setEndDate(\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }


}