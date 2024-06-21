<?php

namespace App\Board\Controller;

use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardController extends AbstractController
{

    /** @Route("/board/index", name="board_index") */
    public function index(TableRepository $tableRepository): Response
    {
        return $this->render(
            '_board/index.html.twig',
            [
                'lists' => $tableRepository->findBy([], ['position' => 'ASC'])
            ]
        );
    }
}
