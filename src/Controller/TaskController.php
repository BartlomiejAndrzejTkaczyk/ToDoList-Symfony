<?php

namespace App\Controller;


use App\Form\Type\TaskType;
use App\Repository\DatabaseAccess\DbAccessInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    public function __construct(
        private readonly DbAccessInterface $dbAccess
    )
    {

    }

    #[Route('/', name: 'app_task_index')]
    public function index(): Response
    {
        $a = 1;
        return $this->render(
            'to-do-list/task-list.html.twig',
            [
                'taskList' => $this->dbAccess->getAllTask()
            ]
        );
    }


    #[Route('/delete/{id}', name: 'app_task_delete', requirements: ['id' => '\d+'])]
    public function delete($id): Response
    {
        $this->dbAccess->deleteTask($id);
        return $this->redirectToRoute('app_task_index');
    }


    #[Route('/add/', name: 'app_task_add', methods: ['GET'])]
    public function add(): Response
    {
        $task = $this->dbAccess->createTaskTemplate();
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

    #[Route('/add/', name: 'app_task_addPost', methods: ['POST'])]
    public function addPost(Request $request): Response
    {
        try {
            $task = $this->dbAccess->createTaskTemplate();
            $form = $this->createForm(
                TaskType::class,
                $task
            );
            $form->handleRequest($request);
            $this->dbAccess->addTask($task);

            return $this->redirectToRoute('app_task_index');
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['e' => $e]);
        }
    }

    #[Route('/update/{id}', name: "app_task_update", methods: ['GET'])]
    public function update($id): Response
    {

        try {
            $form = $this->createForm(
                TaskType::class,
                $this->dbAccess->getTaskById($id),
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
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['e' => $e]);
        }
    }

    #[Route("/update/{id}", name: 'app_task_updatePost', methods: ['POST'])]
    public function updatePost(int $id, Request $request): Response
    {
        try {
            $task = $this->dbAccess->getTaskById($id);
            $form = $this->createForm(
                TaskType::class,
                $task
            );
            $form->handleRequest($request);
            $this->dbAccess->save();

            return $this->redirectToRoute('app_task_index');
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['e' => $e]);
        }
    }
}