<?php

namespace App\Repository;

use App\Entity\DatabaseEntity\Task;
use App\Entity\DTOEntity\TaskChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                 $registry,
        private readonly UserRepository $userRepository //todo
    )
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createFromTaskChange(TaskChange $taskChange)
    {
        $task = new Task();

        $task->setName($taskChange->getName());
        $task->setEndDate($taskChange->getEndDate());
        $task->setUser(
            $this->userRepository->find($taskChange->getUserId())
        );
        $task->setIsFinish($taskChange->isFinish());

        $this->add($task, true);
    }

    public function removeById(int $taskId)
    {
        $this->remove(
            $this->find($taskId),
            true
        );
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush() //todo is this a good idea
    {
        $this->getEntityManager()->flush();
    }

    public function update(Task $task, $taskId): void
    {
        $dbTask = $this->find($taskId);

        if (!$dbTask) {
            throw new NotFoundHttpException('Not found task with id = ' . $task->getId());
        }

        $dbTask->setName($task->getName());

        $this->_em->flush();
    }


}
