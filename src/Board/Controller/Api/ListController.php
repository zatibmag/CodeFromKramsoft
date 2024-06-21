<?php

namespace App\Board\Controller\Api;

use App\Entity\Table;
use App\Repository\TableRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/lists", name="lists_") */
class ListController extends AbstractController
{
    /** @Route("/", name="get", methods={"GET"}) */
    public function index(TableRepository $tableRepository): JsonResponse
    {
        $lists = $tableRepository->findAll();

        return $this->json(\array_map(fn (Table $list) => $list->toArray(), $lists));
    }

    /** @Route("/", name="create", methods={"POST"}) */
    public function create(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $requiredFields = ['title', 'position'];
        foreach ($requiredFields as $field) {
            if (!$request->request->has($field)) {
                return new JsonResponse(['error' => 'Missing required field'], 400);
            }
        }

        $list    = new Table($request->request->get('title'), (int)$request->request->get('position'));
        $manager = $managerRegistry->getManager();
        $manager->persist($list);
        $manager->flush();

        return $this->redirect($this->generateUrl('board_index'));
    }

    /** @Route("/{list}", name="delete", methods={"DELETE"}) */
    public function delete(Table $list, ManagerRegistry $managerRegistry): JsonResponse
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($list);
        $manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
