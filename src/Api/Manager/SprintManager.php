<?php

namespace App\Api\Manager;

use App\Entity\Api\Sprint;
use App\Repository\Api\SprintRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SprintManager
{
    private SprintRepository $sprintRepository;

    public function __construct(SprintRepository $sprintRepository)
    {
        $this->sprintRepository = $sprintRepository;
    }

    public function getSprintByIdOrCurrent(?int $sprintId): Sprint
    {
        $sprint = !!$sprintId
            ? $this->sprintRepository->find($sprintId)
            : $this->sprintRepository->getCurrentSprint();

        if (!$sprint) {
            throw new NotFoundHttpException('Sprint not found');
        }

        return $sprint;
    }
}
