<?php

namespace App\Tests\Api\Preparer;

use App\Api\Preparer\ChartLinePreparer;
use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use App\Entity\Api\SprintExcludedDay;
use App\Entity\CapacityDayChartPoint;
use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use DateTime;

class ChartLinePreparerTest extends TestCase
{
    private $sprint;
    private $chartLine;
    private $preparer;

    protected function setUp(): void
    {
        $this->sprint    = $this->createMock(Sprint::class);
        $this->chartLine = new ChartLine();
        $this->preparer  = new ChartLinePreparer();
    }

    /**
     * @dataProvider chartLineDataProvider
     */
    public function testPrepareChartLine(
        ChartLine $chartLine,
        DateTime $startAt,
        DateTime $endAt,
        int $sprintCapacity,
        array $sprintStories,
        array $expectedValues
    ): void {
        $this->sprint->method('getSprintStories')->willReturn(new ArrayCollection($sprintStories));
        $this->sprint->method('getStartAt')->willReturn($startAt);
        $this->sprint->method('getEndAt')->willReturn($endAt);
        $this->sprint->method('getCapacity')->willReturn($sprintCapacity);

        $result = $this->preparer->prepareChartLine($this->sprint, $chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);

        $chartPoints = $chartLine->getChartPoints()->toArray();

        $this->assertCount(count($expectedValues), $chartPoints);
    }

    public function chartLineDataProvider(): iterable
    {
        $chartLine = new ChartLine();

        $sprintStory1 = $this->createMock(SprintStory::class);
        $sprintStory1->method('getCreatedAt')->willReturn(new DateTime('2024-04-15'));
        $sprintStory1->method('getCapacity')->willReturn(90);

        $sprintStory2 = $this->createMock(SprintStory::class);
        $sprintStory2->method('getCreatedAt')->willReturn(new DateTime('2024-04-16'));
        $sprintStory2->method('getCapacity')->willReturn(80);

        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-04-15'));
        $chartPoint1->setValue(10);
        $chartLine->addChartPoint($chartPoint1);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-04-16'));
        $chartPoint2->setValue($sprintStory2->getCapacity());
        $chartLine->addChartPoint($chartPoint2);

        yield [
            $chartLine,
            new DateTime('2024-04-15'),
            new DateTime('2024-04-20'),
            100,
            [$sprintStory1, $sprintStory2],
            [10, 20]
        ];
    }

    public function testPrepareChartLineWithStoryAfterEndDate(): void
    {
        $sprintStory1 = $this->createMock(SprintStory::class);
        $sprintStory1->method('getCreatedAt')->willReturn(new DateTime('2024-04-21'));
        $sprintStory1->method('getCapacity')->willReturn(5);

        $this->sprint->method('getSprintStories')->willReturn(new ArrayCollection([$sprintStory1]));
        $this->sprint->method('getStartAt')->willReturn(new DateTime('2024-04-15'));
        $this->sprint->method('getEndAt')->willReturn(new DateTime('2024-04-20'));
        $this->sprint->method('getCapacity')->willReturn(20);

        $result = $this->preparer->prepareChartLine($this->sprint, $this->chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);
        $this->assertCount(0, $result->getChartPoints());
    }

    public function testPrepareChartLineWithStoryBeforeStartDate(): void
    {
        $sprintStory1 = $this->createMock(SprintStory::class);
        $sprintStory1->method('getCreatedAt')->willReturn(new DateTime('2024-04-14'));
        $sprintStory1->method('getCapacity')->willReturn(5);

        $this->sprint->method('getSprintStories')->willReturn(new ArrayCollection([$sprintStory1]));
        $this->sprint->method('getStartAt')->willReturn(new DateTime('2024-04-15'));
        $this->sprint->method('getEndAt')->willReturn(new DateTime('2024-04-20'));
        $this->sprint->method('getCapacity')->willReturn(20);

        $result = $this->preparer->prepareChartLine($this->sprint, $this->chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);
        $this->assertCount(0, $result->getChartPoints());
    }

    public function testPreparePerfectChartLineWithNoExcludedDays(): void
    {
        $this->sprint->method('getStartAt')->willReturn(new DateTime('2024-04-15'));
        $this->sprint->method('getEndAt')->willReturn(new DateTime('2024-04-20'));
        $this->sprint->method('getCapacity')->willReturn(100);
        $this->sprint->method('getExcludedDays')->willReturn(new ArrayCollection());

        $result = $this->preparer->preparePerfectChartLine($this->sprint, $this->chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);
        $chartPoints = $result->getChartPoints()->toArray();

        $this->assertCount(6, $chartPoints);

        $expectedValues = [100, 80, 60, 40, 20, 0];

        foreach ($chartPoints as $index => $chartPoint) {
            $this->assertEquals($expectedValues[$index], $chartPoint->getValue());
        }
    }

    public function testPreparePerfectChartLineWithExcludedDaysInRange(): void
    {
        $excludedDay1 = new SprintExcludedDay();
        $excludedDay1->setDate(new DateTime('2024-04-16'));

        $excludedDay2 = new SprintExcludedDay();
        $excludedDay2->setDate(new DateTime('2024-04-17'));

        $this->sprint->method('getExcludedDays')->willReturn(new ArrayCollection([$excludedDay1, $excludedDay2]));

        $this->sprint->method('getStartAt')->willReturn(new DateTime('2024-04-15'));
        $this->sprint->method('getEndAt')->willReturn(new DateTime('2024-04-20'));
        $this->sprint->method('getCapacity')->willReturn(100);

        $result = $this->preparer->preparePerfectChartLine($this->sprint, $this->chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);

        $this->assertCount(6, $result->getChartPoints());

        $expectedValues = [100, 66, 33, 0, -33, -66];

        $chartPoints = $result->getChartPoints()->toArray();
        foreach ($chartPoints as $index => $chartPoint) {
            $this->assertEquals($expectedValues[$index], $chartPoint->getValue(), '', 0.0001);
        }
    }

    /**
     * @dataProvider prepareCapacityDataProvider
     */
    public function testPrepareCapacity(
        DateTime $startDate,
        DateTime $endDate,
        int $capacityValue,
        array $expectedValues
    ): void {
        $result = $this->preparer->prepareCapacity($startDate, $endDate, $capacityValue, $this->sprint);

        $this->assertInstanceOf(ChartLine::class, $result);

        $chartPoints = $result->getChartPoints()->toArray();

        $this->assertCount(6, $chartPoints);

        foreach ($chartPoints as $index => $chartPoint) {
            $this->assertEquals($expectedValues[$index], $chartPoint->getValue());
        }
    }

    public function prepareCapacityDataProvider(): iterable
    {
        yield [
            new DateTime('2024-04-15'),
            new DateTime('2024-04-20'),
            100,
            [100, 80, 60, 40, 20, 0],
        ];
    }

    /**
     * @dataProvider prepareCustomChartLineDataProvider
     */
    public function testPrepareCustomChartLine(
        ChartLine $chartLine,
        array $expectedValues
    ): void {
        $this->sprint
            ->expects($this->exactly(1))
            ->method('getStartAt')
            ->willReturn(new DateTime('2024-04-15'));

        $this->sprint
            ->expects($this->exactly(3))
            ->method('getEndAt')->willReturn(new DateTime('2024-04-20'));

        $this->sprint
            ->expects($this->exactly(3))
            ->method('getExcludedDays')->willReturn(new ArrayCollection());

        $result = $this->preparer->prepareCustomChartLine($this->sprint, $chartLine);

        $this->assertInstanceOf(ChartLine::class, $result);

        $chartPoints = $result->getChartPoints()->toArray();

        $this->assertCount(count($expectedValues), $chartPoints);

        foreach ($chartPoints as $index => $chartPoint) {
            $this->assertEquals($expectedValues[$index], $chartPoint->getValue());
        }
    }

    public function prepareCustomChartLineDataProvider(): iterable
    {
        $chartLine = new ChartLine();
        $chartLine->setCapacity(100);

        $capacityDay1 = new CapacityDayChartPoint();
        $capacityDay1->setDate(new DateTime('2024-04-16'));
        $capacityDay1->setValue(90);

        $capacityDay2 = new CapacityDayChartPoint();
        $capacityDay2->setDate(new DateTime('2024-04-18'));
        $capacityDay2->setValue(80);

        $chartLine->setCapacityDayChartPoints(new ArrayCollection([
            $capacityDay1,
            $capacityDay2,
        ]));

        yield [
            $chartLine,
            [100, 80, 90, 67, 45, 80, 40, 0],
        ];
    }
}

