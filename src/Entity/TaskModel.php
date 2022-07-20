<?php

namespace App\Entity;

use App\Entity\Exception\WrongDateException;
use App\Utils\PriorityTask;
use DateTime;


class TaskModel
{
    private ?DateTime $creatDate;
    // can be null
    private  ?DateTime $endDate;
    private string $name;
    private PriorityTask $priority;

    /**
     * @throws WrongDateException
     */
    public function __construct(string $name , \DateTime $endDate = null, PriorityTask $priority = PriorityTask::Low)
    {
//        $this->creatDate = \DateTime::createFromFormat(DATE_W3C ,date(DATE_W3C));
        $this->creatDate = new DateTime(date(DATE_W3C));

        if($endDate != null && $endDate < $this->creatDate){
            throw new WrongDateException('end date can\'t be before create date');
        }
        print 'a';
        $this->endDate = $endDate;
        $this->name = $name;
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatDate(): ?DateTime
    {
        return $this->creatDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @return PriorityTask
     */
    public function getPriority(): PriorityTask
    {
        return $this->priority;
    }



}