<?php

namespace App\Api\Preparer;

use App\Entity\Api\EntityInterface;
use App\Entity\ChartLine;
use App\Form\Api\SprintChartLineType;

class SprintChartLineFormPreparer extends FormPreparer
{
    protected string $formType = SprintChartLineType::class;

    protected function support(EntityInterface $entity): bool
    {
        return $entity instanceof ChartLine;
    }
}
