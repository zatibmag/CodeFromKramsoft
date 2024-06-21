<?php

namespace App\Api\Preparer;

use App\Entity\Api\EntityInterface;
use App\Entity\CapacityDayChartPoint;
use App\Form\Api\CapacityDayChartPointType;

class CapacityDayChartPointFormPreparer extends FormPreparer
{
    protected string $formType = CapacityDayChartPointType::class;

    protected function support(EntityInterface $entity): bool
    {
        return $entity instanceof CapacityDayChartPoint;
    }
}
