<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\DTOEntity\TaskChange;
use App\Form\TaskChangeType;
use App\Query\DbalTaskQuery;
use App\Query\DbalUserQuery;
use App\Repository\TaskRepository;
use App\UseCase\UpdateTaskUseCase;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    private readonly string $userEmail;

    public function __construct(
        private readonly TaskRepository    $taskRepository,
        private readonly DbalTaskQuery     $taskQuery,
        private readonly UpdateTaskUseCase $updateTask,
        private readonly DbalUserQuery     $userQuery
    )
    {

    }

    #[Route('/task', name: 'task_index', methods: ['GET'])]
    public function index(): Response
    {
        $email = $this->getUser()->getUserIdentifier();

        try {
            $taskList = $this->taskQuery->findAllByUserEmail($email); // todo zwracaj posortowane obiekty
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        return $this->render('to-do-list/task-list.html.twig', [ // todo standarody zapis dla symfony
            'activeTaskList' => array_filter($taskList, 'App\Utils\FilterArray::isActive'),
            'finishTaskList' => array_filter($taskList, 'App\Utils\FilterArray::isFinish'),
        ]);
    }


    #[Route('/delete/{taskId}', name: 'task_delete', requirements: ['taskId' => '\d+'], methods: ['DELETE'])]
    public function delete(int $taskId): Response
    {
        try {
            $this->checkPermission($taskId);
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        $task = $this->taskRepository->find($taskId);

        if(!$task){
            return $this->render('exception-site.html.twig', ['str' => 'Not found']);
        }

        $this->taskRepository->remove($task, true);
        return $this->redirectToRoute('task_index');
    }


    #[Route('/add/', name: 'task_add', methods: ['GET'])] // todo złącz
    public function add(): Response
    {
        $form = $this->createForm(
            TaskChangeType::class,
            new TaskChange(),
            [
                'action' => $this->generateUrl('task_addPost'),
            ]
        );

        return $this->render(
            'to-do-list/task-form.html.twig',
            [
                'form' => $form->createView(),
                'destination' => 'task_addPost',
            ]
        );
    }


    #[Route('/add/', name: 'task_addPost', methods: ['POST'])]
    public function addPost(Request $request): Response
    {
        $task = new TaskChange();

        $form = $this->createForm(TaskChangeType::class, $task);

        $form->handleRequest($request);

        try {
            $task->setUserId(
                $this->userQuery->findIdByEmail($this->getUser()->getUserIdentifier())
            );
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        $this->taskRepository->createFromTaskChange($task); //todo zrób fabrykę

        return $this->redirectToRoute('task_index');
    }

    #[Route('/update/{taskId}', name: "task_update", methods: ['GET'])]
    public function update(int $taskId): Response
    {
        try {
            $this->checkPermission($taskId);

            $task = $this->taskQuery->findById($taskId);
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        $form = $this->createForm(
            TaskChangeType::class,
            $task,
            ['action' => $this->generateUrl(
                'task_updatePost',
                [
                    'taskId' => $taskId,
                ]
            ),
            ]
        );

        return $this->render('to-do-list/task-form.html.twig', [
            'form' => $form->createView(),
            'data' => [
                'taskId' => $taskId,
            ],
            'destination' => 'task_updatePost'
        ]);
    }


    #[Route("/update/{taskId}", name: 'task_updatePost', methods: ['POST'])]
    public function updatePost(Request $request, int $taskId): Response
    {
        $task = new TaskChange();

        $task->setId($taskId);

        try {
            $this->checkPermission($taskId);
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception]);
        }

        $form = $this->createForm(TaskChangeType::class, $task);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->redirectToRoute('task_index');
        }

        if (!$form->isValid()) {
            $this->render('exception-site.html.twig', ['str' => 'Invalid data']);
        }

        try {
            $this->updateTask->execute($task);

            return $this->redirectToRoute('task_index');
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }
    }

    #[Route('finish/{taskId}', name: 'task_finish')]
    public function finishTask(int $taskId): Response
    {
        try {
            $this->checkPermission($taskId);
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception]);
        }

        $task = new TaskChange();
        $task->setId($taskId);
        $task->setIsFinish(true);

        $this->updateTask->execute($task);

        return $this->redirectToRoute('task_index');
    }

    private function checkPermission(int $taskId): void
    {
        $userId = $this->userQuery->findIdByEmail($this->getUser()->getUserIdentifier());

        if (!$this->taskQuery->isTaskOwner($taskId, $userId)) {
            throw new Exception('Access denied');
        }
    }
}