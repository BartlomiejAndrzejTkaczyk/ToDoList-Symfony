<?php

namespace App\Query;

use App\Entity\DTOEntity\TaskChange;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use App\Entity\DTOEntity\TaskForEmail;

class DbalTaskQuery
{

    public function __construct(
        private Connection    $connection,
        private DbalUserQuery $userQuery
    )
    {
    }

    /**
     * @throws Exception
     */
    public function findById(int $id): TaskChange
    {
        $qb = $this->connection->createQueryBuilder();
        $taskDbal = $qb
            ->select('t.id')
            ->addSelect('t.name')
            ->addSelect('t.end_date')
            ->addSelect('t.user_id')
            ->addSelect('t.is_finish')
            ->from('task', 't')
            ->where(
                $qb->expr()->eq('t.id', ':id')
            )
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
        return TaskChange::createFromDbal($taskDbal);
    }


    /**
     * @throws Exception
     */
    public function isTaskOwner($taskId, $userId): bool
    {

        $task = $this->findById($taskId);

        if ($task->getUserId() == $userId) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function findAllByUserEmail(string $email): array
    {
        $qb = $this->connection->createQueryBuilder();


        $userId = $this->userQuery->findIdByEmail($email);

        $tasksDbal = $qb
            ->select('t.id')
            ->addSelect('t.name')
            ->addSelect('t.end_date')
            ->addSelect('t.user_id')
            ->addSelect('t.is_finish')
            ->from('task', 't')
            ->where(
                $qb->expr()->eq('t.user_id', ':userId')
            )
            ->setParameter('userId', $userId)
            ->executeQuery()
            ->fetchAllAssociative();

        /** @var TaskChange[] $tasks */
        $tasks = [];

        foreach ($tasksDbal as $item) {
            $tasks[] = TaskChange::createFromDbal($item);
        }

        return $tasks;
    }

    public function getCommingTaskWithUser(): array
    {
        /** @var TaskForEmail[] $res */
        $res = [];

        $qb = $this->connection->createQueryBuilder();

        $today = new \DateTimeImmutable();

        $qb
            ->select('u.email')
            ->addSelect('t.name')
            ->from('User', 'u')
            ->innerJoin('u', 'Task', 't', 'u.id = t.user_id')
            ->where(
                $qb->expr()->lte('t.end_date', $today->modify('+3 day')->format("'Y-m-d H:i:s'"))
            )
            ->andWhere(
                $qb->expr()->gte('t.end_date', $today->format("'Y-m-d H:i:s'"))
            )
            ->orderBy('u.email');

        return TaskForEmail::createFromDbal( $qb->executeQuery()->fetchAllAssociative());
    }

}