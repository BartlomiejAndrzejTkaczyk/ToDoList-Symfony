<?php

namespace App\Controller;

use App\Entity\Exception\WrongDateException;
use App\Entity\TaskModel;
use App\Repository\DatabaseAccess\DbAccessInterface;
use App\Repository\DatabaseAccess\FakeDbAccess;
use phpDocumentor\Reflection\Types\Self_;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private static DbAccessInterface $dbAccess;

    public function __construct(LoggerInterface $logger)
    {
        self::$dbAccess = new FakeDbAccess($logger);

    }

    /**
     * @throws WrongDateException
     */
    #[Route('/', name: 'main_index')]
    public function index(): Response
    {
        return $this->render(
            'to-do-list/task-list.html.twig',
            [
                'taskList' => self::$dbAccess->getAllTask()
            ]
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('/del/{id}', name: 'main_del_task', requirements: ['id' => '\d+'])]
    public function del($id): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        self::$dbAccess->delTask($id);
        return $this->redirectToRoute('main_index');
    }

    /**
     * @throws WrongDateException
     */
    #[Route('/add/{name}')]
    public function add(string $name): \Symfony\Component\HttpFoundation\RedirectResponse
    {

        // todo add try
        self::$dbAccess->addTask(new TaskModel($name));
        return $this->redirectToRoute('main_index');
    }

    #[Route('/edit/{id}/{name}', name: 'main_edit_task')]
    public function edit(int $id, string $name) : Response
    {
        $code = self::$dbAccess->editTask($id, $name);
        if ($code == 200){
            return $this->redirectToRoute('main_index');
        }
        return new Response(status: $code);
    }
}