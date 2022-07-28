<?php

namespace App\Query;

use Doctrine\DBAL\Connection;

class DbalUserQuery
{

    public function __construct(
        private readonly Connection $connection
    )
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findEmailOwner(int $userId): string
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('u.email')
            ->from('user', 'u')
            ->where(
                $qb->expr()->eq('u.id', ':id')
            )
            ->setParameter('id', $userId);
        return $qb->executeQuery()->fetchOne();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findIdByEmail(string $email): int
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('u.id')
            ->from('User', 'u')
            ->where(
                $qb->expr()->eq('u.email', ':userEmail')
            )
            ->setParameter('userEmail', $email);

        return $qb->executeQuery()->fetchOne();
    }


}