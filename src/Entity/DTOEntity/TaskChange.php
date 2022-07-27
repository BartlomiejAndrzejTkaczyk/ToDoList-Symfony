<?php

namespace App\Entity\DTOEntity;

use App\Entity\DatabaseEntity\Task;
use App\Utils\TaskStatus;

class TaskChange
{
    private int $id;
    private string $name;
    private \DateTimeImmutable $endDate;
    private bool $isFinish;

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
        $task->setIsFinish(
            $dbal['is_finish']
        );
        return $task;
    }

    public function __construct()
    {
        $this->setEndDate(new \DateTimeImmutable());
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


    /**
     * @return TaskStatus
     */
    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    /**
     * @param TaskStatus $status
     */
    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isFinish(): bool
    {
        return $this->isFinish;
    }

    /**
     * @param bool $isFinish
     */
    public function setIsFinish(bool $isFinish): void
    {
        $this->isFinish = $isFinish;
    }



}