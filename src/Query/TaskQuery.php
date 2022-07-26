<?php

namespace App\Query;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskQuery
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getTaskById(int $id)
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('t')
            ->from('App:Task', 't')
            ->where(

            )
            ->getQuery()
            ->execute();
    }

    public function getAllTaskForUser(User $user): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('t')
            ->from('App:Task', 't')
            ->getQuery()
            ->execute();
    }
}