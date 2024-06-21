<?php

namespace App\Api\Preparer;

use App\Entity\Api\EntityInterface;
use App\Entity\Api\Sprint;
use App\Form\Api\SprintType;

class SprintFormPreparer extends FormPreparer
{
    protected string $formType = SprintType::class;

    protected function support(EntityInterface $entity): bool
    {
        return $entity instanceof Sprint;
    }
}
