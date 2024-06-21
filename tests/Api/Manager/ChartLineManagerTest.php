<?php

namespace App\Tests\Api\Manager;

use App\Api\Manager\ChartLineManager;
use App\Api\Preparer\ChartLinePreparer;
use App\Entity\Api\Sprint;
use App\Entity\ChartLine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ChartLineManagerTest extends TestCase
{
    private $entityManager;
    private $chartLinePreparer;
    private $chartLineManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->chartLinePreparer = $this->createMock(ChartLinePreparer::class);
        $this->chartLineManager = new ChartLineManager($this->entityManager, $this->chartLinePreparer);
    }

    public function sprintDataProvider(): array
    {
        $existingChartLine = $this->createMock(ChartLine::class);
        $newChartLine = $this->createMock(ChartLine::class);

        return [
            'sprint with no perfect chart line' => [null, $newChartLine],
            'sprint with existing perfect chart line' => [$existingChartLine, $newChartLine],
        ];
    }

    /**
     * @dataProvider sprintDataProvider
     */
    public function testUpdatePerfectChartLine(?ChartLine $existingChartLine, ChartLine $preparedChartLine)
    {
        $sprint = $this->createMock(Sprint::class);

        $sprint->method('getPerfectChartLine')->willReturn($existingChartLine);
        $this->chartLinePreparer->method('preparePerfectChartLine')->willReturn($preparedChartLine);

        $sprint->expects($this->once())->method('setPerfectChartLine')->with($preparedChartLine);
        $this->entityManager->expects($this->exactly(2))->method('persist')->withConsecutive([$preparedChartLine], [$sprint]);
        $this->entityManager->expects($this->once())->method('flush');

        $this->chartLineManager->updatePerfectChartLine($sprint);
    }

    public function updateCurrentChartLineDataProvider(): array
    {
        $existingChartLine = $this->createMock(ChartLine::class);
        $newChartLine = $this->createMock(ChartLine::class);

        return [
            'sprint with no current chart line' => [null, $newChartLine],
            'sprint with existing current chart line' => [$existingChartLine, $newChartLine],
        ];
    }

    /**
     * @dataProvider updateCurrentChartLineDataProvider
     */
    public function testUpdateCurrentChartLine(?ChartLine $existingChartLine, ChartLine $preparedChartLine)
    {
        $sprint = $this->createMock(Sprint::class);

        $sprint->method('getCurrentChartLine')->willReturn($existingChartLine);

        $this->chartLinePreparer->method('prepareChartLine')->willReturn($preparedChartLine);

        $sprint->expects($this->once())->method('setCurrentChartLine')->with($preparedChartLine);
        $this->entityManager->expects($this->exactly(2))->method('persist')->withConsecutive([$preparedChartLine], [$sprint]);
        $this->entityManager->expects($this->once())->method('flush');

        $this->chartLineManager->updateCurrentChartLine($sprint);
    }

    public function updateAdditionalChartLinesDataProvider(): array
    {
        $chartLine1 = $this->createMock(ChartLine::class);
        $chartLine2 = $this->createMock(ChartLine::class);

        return [
            'sprint with no chart lines' => [new ArrayCollection([])],
            'sprint with chart lines' => [new ArrayCollection([$chartLine1, $chartLine2])],
        ];
    }

    /**
     * @dataProvider updateAdditionalChartLinesDataProvider
     */
    public function testUpdateAdditionalChartLines(ArrayCollection $chartLines)
    {
        $sprint = $this->createMock(Sprint::class);
        $sprint->method('getChartLines')->willReturn($chartLines);

        $this->chartLinePreparer->expects($this->exactly($chartLines->count()))
            ->method('preparePerfectChartLine')
            ->willReturn($this->createMock(ChartLine::class));

        $this->entityManager->expects($this->exactly($chartLines->count() * 2))->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->chartLineManager->updateAdditionalChartLines($sprint);
    }
}
