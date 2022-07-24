<?php

namespace App\Controller;

use App\Entity\Exception\WrongDateException;
use App\Entity\TaskModel;
use App\Repository\DatabaseAccess\DbAccessInterface;
use App\Repository\DatabaseAccess\FakeDbAccess;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private static DbAccessInterface $dbAccess;

    public function __construct(LoggerInterface $logger)
    {
        self::$dbAccess = new FakeDbAccess($logger);
    }

    #[Route('/', name: 'app_task_index')]
    public function index(): Response
    {
        try {
            return $this->render(
                'to-do-list/task-list.html.twig',
                [
                    'taskList' => self::$dbAccess->getAllTask()
                ]
            );
        } catch (WrongDateException $e) {
            return $this->render(
                'exception-site.html.twig',
                [
                    'e' => $e,
                ]
            );
        }
    }


    #[Route('/delete/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'])]
    public function delete($id): Response
    {
        try {
            self::$dbAccess->deleteTask($id);
            return $this->redirectToRoute('app_tasl_index');
        } catch (Exception $e) {
            return $this->render(
                'exception-site.html.twig',
                [
                    'e' => $e,
                ]
            );
        }
    }

    /**
     * @throws WrongDateException
     */
    #[Route('/add/{name}')]
    public function add(string $name): \Symfony\Component\HttpFoundation\RedirectResponse
    {

        // todo add try
        self::$dbAccess->addTask(new TaskModel($name));
        return $this->redirectToRoute('app_task_index');
    }

    #[Route("/update/{id}", name: 'app_task_update', methods: ['POST'])]
    public function editPost(int $id): Response
    {
        return new Response($_POST['newName'] . ' ' . $id);
    }

    #[Route('/update/{id}', name: "app_task_update", methods: ['GET'])]
    public function edit($id): Response
    {
        return $this->render(
            'to-do-list/task-edit.html.twig',
            [
                'name' => 'Nic',
                'id' => $id
            ]
        );
    }
}