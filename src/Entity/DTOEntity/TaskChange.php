<?php

namespace App\Entity\DTOEntity;

use App\Entity\DatabaseEntity\Task;
use App\Utils\TaskStatus;
use mysql_xdevapi\TableSelect;

class TaskChange // todo rozbij na dwa osobne klasy
{
    private ?int $id = null;
    private ?string $name = null;
    private ?\DateTimeImmutable $endDate = null;
    private ?bool $isFinish = null;

    private ?int $userId = null;
    private ?string $email = null;



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
        $this->setIsFinish(false);
        $this->setEndDate(new \DateTimeImmutable());
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeImmutable|null $endDate
     */
    public function setEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return bool|null
     */
    public function IsFinish(): ?bool
    {
        return $this->isFinish;
    }

    /**
     * @param bool|null $isFinish
     */
    public function setIsFinish(?bool $isFinish): void
    {
        $this->isFinish = $isFinish;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }




}