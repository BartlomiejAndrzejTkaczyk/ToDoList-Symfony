<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\DatabaseEntity\Task;
use App\Entity\DTOEntity\TaskChange;
use App\Form\TaskType;
use App\Form\TaskChangeType;
use App\Query\DbalTaskQuery;
use App\Query\DbalUserQuery;
use App\Repository\TaskRepository;
use App\UseCase\UpdateTaskUseCase;
use App\Utils\FilterArray;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    #[Route('/task', name: 'task_index')]
    public function index(): Response
    {
        $email = $this->getUser()->getUserIdentifier();

        try {
            $taskList = $this->taskQuery->findAllByUserEmail($email);
            return $this->render(
                'to-do-list/task-list.html.twig',
                [
                    'activeTaskList' => array_filter($taskList, 'App\Utils\FilterArray::isActive'),
                    'finishTaskList' => array_filter($taskList, 'App\Utils\FilterArray::isFinish'),
                    'userId' => $this->userQuery->findIdByEmail($email),
                ]
            );
        } catch (Exception $exception) {

            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }
    }


    #[Route('/delete/{taskId}', name: 'task_delete', requirements: ['taskId' => '\d+'])]
    public function delete(int $taskId): Response
    {
        try {
            $this->permissionToResource($taskId);
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        $this->taskRepository->removeById($taskId);
        return $this->redirectToRoute('task_index');
    }


    #[Route('/add/', name: 'task_add', methods: ['GET'])]
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

        $form = $this->createForm(
            TaskChangeType::class,
            $task
        );

        $form->handleRequest($request);

        try {
            $task->setUserId(
                $this->userQuery->findIdByEmail($this->getUser()->getUserIdentifier())
            );
        } catch (Exception $exception) {
            return $this->render('exception-site.html.twig', ['str' => $exception->getMessage()]);
        }

        $this->taskRepository->createFromTaskChange($task);

        return $this->redirectToRoute('task_index');
    }

    #[Route('/update/{taskId}', name: "task_update", methods: ['GET'])]
    public function update(int $userId, int $taskId): Response
    {
        try {
            $this->permissionToResource($taskId);

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
                    'userId' => $userId,
                    'taskId' => $taskId,
                ]
            ),
            ]
        );

        return $this->render('to-do-list/task-form.html.twig', [
            'form' => $form->createView(),
            'data' => [
                'userId' => $userId,
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
            $this->permissionToResource($taskId);
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
    public function setTaskAsFinish(int $taskId): RedirectResponse|Response
    {
        try{
            $this->permissionToResource($taskId);
        } catch (Exception $exception){
            return $this->render('exception-site.html.twig', ['str' => $exception]);
        }

        $task = new TaskChange();
        $task->setId($taskId);
        $task->setIsFinish(true);

        $this->updateTask->execute($task);

        return $this->redirectToRoute('task_index');
    }

    private function permissionToResource(int $taskId): void
    {
        $userId = $this->userQuery->findIdByEmail($this->getUser()->getUserIdentifier());

        if (!$this->taskQuery->isTaskOwner($taskId, $userId)) {
            throw new Exception('Access denied');
        }
    }
}