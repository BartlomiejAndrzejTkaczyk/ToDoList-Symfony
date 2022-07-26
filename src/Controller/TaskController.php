<?php

namespace App\Controller;


use App\Entity\Task;
use App\Entity\User;
use App\Form\Type\TaskType;
use App\Query\TaskQuery;
use App\Repository\DatabaseAccess\DbAccessInterface;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{

    public function __construct(
        private readonly TaskRepository $taskRepository,
//        private readonly TaskQuery $taskQuery
    )
    {
    }

    #[Route('/', name: 'task_index')]
    public function index(UserInterface $user, UserRepository $userRepository): Response
    {

        return $this->render(
            'to-do-list/task-list.html.twig',
            [
                'taskList' => $this->taskRepository->findBy(
                    [
                        'user' => $userRepository->findBy(
                            [
                                'email' => $user->getUserIdentifier()
                            ]
                        )
                    ]
                )
            ]
        );
    }


    #[Route('/delete/{id}', name: 'task_delete', requirements: ['id' => '\d+'])]
    public function delete($id): Response
    {
        $this->taskRepository->removeById($id);
        return $this->redirectToRoute('task_index');
    }


    #[Route('/add/', name: 'task_add', methods: ['GET'])]
    public function add(): Response
    {

        $form = $this->createForm(
            TaskType::class,
            new Task(), // todo is that good
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
    public function addPost(Request $request, UserInterface $user, UserRepository $userRepository): Response
    {
        try {
            $task = new Task();

            $form = $this->createForm(
                TaskType::class,
                $task
            );


            $form->handleRequest($request);

            $task->setUser(
                $userRepository->findOneBy(
                    ['email' => $user->getUserIdentifier()]
                )
            );

            $this->taskRepository->add($task, true);

            return $this->redirectToRoute('task_index');
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['exception' => $e]);
        }
    }

    #[Route('/update/{id}', name: "task_update", methods: ['GET'])]
    public function update($id): Response
    {

        try {
            $form = $this->createForm(
                TaskType::class,
                $this->taskRepository->find($id), // query
                [
                    'action' => $this->generateUrl('task_updatePost', ['id' => $id]),
                ]
            );


            return $this->render(
                'to-do-list/task-form.html.twig',
                [
                    'form' => $form->createView(),
                    'data' => ['id' => $id],
                    'destination' => 'task_updatePost'
                ]
            );
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['exception' => $e]);
        }
    }

    #[Route("/update/", name: 'task_updatePost', methods: ['POST'])]
    public function updatePost(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(
            TaskType::class,
            $task
        );
        dd($task);
        $form->handleRequest($request);

        try {
            $this->taskRepository->update($task);
        } catch (Exception $e) {
            return $this->render('exception-site.html.twig', ['exception' => $e]);
        }

        return $this->redirectToRoute('task_index');
    }
}