<?php

namespace App\Board\Controller\Api;

use App\Entity\Table;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/tasks", name="tasks_") */
class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /** @Route("/", name="get", methods={"GET"}) */
    public function index(): JsonResponse
    {
        $tasks = $this->taskRepository->findAll();
        $tasks = array_map(fn($task) => $task->toArray(), $tasks);

        return $this->json($tasks);
    }

    /**
     * @Route("/move/{task}/{list}", name="move", methods={"PUT"})
     * @Entity("task", expr="repository.find(task)")
     * @Entity("list", expr="repository.find(list)")
     */
    public function move(Task $task, Table $list, ManagerRegistry $managerRegistry): JsonResponse
    {
        $task->move($list);
        $manager = $managerRegistry->getManager();
        $manager->persist($task);
        $manager->flush();

        return $this->json(['task' => $task->toArray()]);
    }

    /**
     * @Route("/{task}", name="delete", methods={"DELETE"})
     * @Entity("task", expr="repository.find(task)")
     */
    public function delete(Task $task, ManagerRegistry $managerRegistry): JsonResponse
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($task);
        $manager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/{list}", name="create", methods={"POST"})
     * @Entity("list", expr="repository.find(list)")
     */
    public function create(Request $request, Table $list, ManagerRegistry $managerRegistry): Response
    {
        if (!$request->request->has('title')) {
            return new JsonResponse(['error' => 'Title is required'], Response::HTTP_BAD_REQUEST);
        }
        $storyPoints = $request->request->get('storyPoints');
        $task        = new Task($request->request->get('title'), $list, $storyPoints ?: 0);
        $manager     = $managerRegistry->getManager();
        $manager->persist($task);
        $manager->flush();

        return $this->redirect($this->generateUrl('board_index'));
    }
}
