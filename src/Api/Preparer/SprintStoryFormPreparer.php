<?php

namespace App\Api\Preparer;

use App\Entity\Api\EntityInterface;
use App\Entity\Api\SprintStory;
use App\Form\Api\SprintStoryType;

class SprintStoryFormPreparer extends FormPreparer
{
    protected string $formType = SprintStoryType::class;

    protected function support(EntityInterface $entity): bool
    {
        return $entity instanceof SprintStory;
    }
}
