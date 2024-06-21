<?php

namespace App\Api\Manager;

use App\Api\Preparer\ChartLinePreparer;
use App\Entity\Api\Sprint;
use App\Entity\ChartLine;
use Doctrine\ORM\EntityManagerInterface;

class ChartLineManager
{
    private EntityManagerInterface $entityManager;
    private ChartLinePreparer      $chartLinePreparer;

    public function __construct(EntityManagerInterface $entityManager, ChartLinePreparer $chartLinePreparer)
    {
        $this->entityManager     = $entityManager;
        $this->chartLinePreparer = $chartLinePreparer;
    }

    public function updatePerfectChartLine(Sprint $sprint): void
    {
        $perfectChartLine = $this->chartLinePreparer->preparePerfectChartLine(
            $sprint,
            $sprint->getPerfectChartLine() ?? new ChartLine()
        );

        $sprint->setPerfectChartLine($perfectChartLine);

        $this->entityManager->persist($perfectChartLine);
        $this->entityManager->persist($sprint);
        $this->entityManager->flush();
    }

    public function updateCurrentChartLine(Sprint $sprint)
    {
        $currentChartLine = $this->chartLinePreparer->prepareChartLine(
            $sprint,
            $sprint->getCurrentChartLine() ?? new ChartLine()
        );

        $sprint->setCurrentChartLine($currentChartLine);

        $this->entityManager->persist($currentChartLine);
        $this->entityManager->persist($sprint);
        $this->entityManager->flush();
    }

    public function updateAdditionalChartLines(Sprint $sprint)
    {
        $chartLines = $sprint->getChartLines();

        foreach ($chartLines as $chartLine) {
            $chartLine = $this->chartLinePreparer->preparePerfectChartLine($sprint, $chartLine);

            $this->entityManager->persist($chartLine);
            $this->entityManager->persist($sprint);
        }

        $this->entityManager->flush();
    }
}
