<?php

namespace App\Controller;

use App\Entity\Exception\WrongDateException;
use App\Entity\TaskModel;
use App\Form\Type\TaskType;
use App\Repository\DatabaseAccess\DbAccessInterface;
use App\Repository\DatabaseAccess\FakeDbAccess;
use App\Utils\PriorityTask;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            return $this->redirectToRoute('app_task_index');
        } catch (Exception $e) {
            return $this->render(
                'exception-site.html.twig',
                [
                    'e' => $e,
                ]
            );
        }
    }


    #[Route('/add/', name: 'app_task_add', methods: ['GET'])]
    public function add(): Response
    {
        $task = self::$dbAccess->createTaskTemplate();
        $form = $this->createForm(
            TaskType::class,
            $task,
            [
                'action' => $this->generateUrl('app_task_addPost'),
                'method' => 'POST',
            ]
        );


        return $this->render(
            'to-do-list/task-form.html.twig',
            [
                'form' => $form->createView(),
                'data' => [],
                'destination' => 'app_task_addPost',
            ]
        );
    }

    #[Route('/add/',name: 'app_task_addPost' ,methods: ['POST'])]
    public function addPost(Request $request): Response
    {
        $task = self::$dbAccess->createTaskTemplate();
        $form = $this->createForm(
            TaskType::class,
            $task
        );
        $form->handleRequest($request);
        self::$dbAccess->addTask($task);

        return $this->redirectToRoute('app_task_index');
    }

    #[Route('/update/{id}', name: "app_task_update", methods: ['GET'])]
    public function update($id): Response
    {

        $form = $this->createForm(
            TaskType::class,
            self::$dbAccess->getTaskById($id),
            [
                'action' => $this->generateUrl('app_task_updatePost', ['id' => $id]),
                'method' => 'POST',
            ]
        );


        return $this->render(
            'to-do-list/task-form.html.twig',
            [
                'form' => $form->createView(),
                'data' => ['id' => $id],
                'destination' => 'app_task_updatePost'
            ]
        );
    }

    #[Route("/update/{id}", name: 'app_task_updatePost', methods: ['POST'])]
    public function updatePost(int $id, Request $request): Response
    {
        $task = self::$dbAccess->getTaskById($id);
        $form = $this->createForm(
            TaskType::class,
            $task
        );
        $form->handleRequest($request);
        self::$dbAccess->save();

        return $this->redirectToRoute('app_task_index');
    }
}