<?php

namespace App\Api\Preparer;

use App\Entity\Api\EntityInterface;
use App\Entity\Api\Sprint;
use App\Form\Api\SprintDaysType;

class SprintDaysFormPreparer extends FormPreparer
{
    protected string $formType = SprintDaysType::class;

    protected function support(EntityInterface $entity): bool
    {
        return $entity instanceof Sprint;
    }
}
