<?php

namespace App\Entity;

use App\Entity\Exception\WrongDateException;
use App\Utils\PriorityTask;
use DateTime;


class TaskModel
{
    private static int $nextId = 0;
    public int $id;
    public ?DateTime $creatDate;
    // can be null
    public  ?DateTime $endDate;
    public string $name;
    public PriorityTask $priority;

    /**
     * @throws WrongDateException
     * @throws \Exception
     */
    public function __construct(string $name , \DateTime $endDate = null, PriorityTask $priority = PriorityTask::Low)
    {
        $this->creatDate = new \DateTime(date(DATE_W3C));

        if($endDate != null && $endDate < $this->creatDate){
            throw new WrongDateException('end date can\'t be before create date');
        }

        $this->id = self::$nextId++;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->priority = $priority;
    }
}