<?php

namespace App\Entity;

use App\Entity\Exception\WrongDateException;
use App\Utils\PriorityTask;
use DateTime;


class TaskModel
{

    /**
     * @throws WrongDateException
     * @throws \Exception
     */
    public function __construct(
        protected int $id,
        protected string $name,
        protected ?DateTime $creatDate,
        protected ?DateTime $endDate = null,
        protected PriorityTask $priority = PriorityTask::Low
    )
    {
        $this->creatDate = new \DateTime(date(DATE_W3C));

        if ($endDate != null && $endDate < $this->creatDate) {
            throw new WrongDateException('end date can\'t be before create date');
        }
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
     * @return DateTime|null
     */
    public function getCreatDate(): ?DateTime
    {
        return $this->creatDate;
    }

    /**
     * @param DateTime|null $creatDate
     */
    public function setCreatDate(?DateTime $creatDate): void
    {
        $this->creatDate = $creatDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     */
    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
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
     * @return PriorityTask
     */
    public function getPriority(): PriorityTask
    {
        return $this->priority;
    }

    /**
     * @param PriorityTask $priority
     */
    public function setPriority(PriorityTask $priority): void
    {
        $this->priority = $priority;
    }


}