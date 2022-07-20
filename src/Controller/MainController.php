<?php

namespace App\Controller;

use App\Repository\Task\FakeTaskRepository;
use App\Repository\Task\TaskRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private TaskRepositoryInterface $tasks;

    public function __construct()
    {
        $this->tasks = new FakeTaskRepository();
    }

    /**
     * @throws \App\Entity\Exception\WrongDateException
     */
    #[Route('/')]
    public function index(): Response
    {

        return $this->render(
            'to-do-list/task-list.html.twig',
            [
                'taskList' => $this->tasks->getAll()
            ]
        );
    }
}