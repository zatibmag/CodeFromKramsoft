<?php

namespace App\Tests\Entity;

use App\Entity\Api\Sprint;
use App\Entity\CapacityDayChartPoint;
use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ChartLineTest extends TestCase
{
    protected ChartLine  $chartLine;
    protected ChartPoint $chartPoint;

    protected function setUp(): void
    {
        $this->chartLine   = new ChartLine();
        $this->chartPoint1 = new ChartPoint();
        $this->chartPoint2 = new ChartPoint();
    }

    /**
     * @dataProvider capacityDataProvider
     */
    public function testSetAndGetCapacity($capacity)
    {
        $this->chartLine->setCapacity($capacity);
        $this->assertEquals($capacity, $this->chartLine->getCapacity());
    }

    public function capacityDataProvider()
    {
        return [
            [null],
            [0],
            [10],
            [-5],
        ];
    }

    /**
     * @dataProvider sprintDataProvider
     */
    public function testSetAndGetSprint($sprint)
    {
        $this->chartLine->setSprint($sprint);
        $this->assertEquals($sprint, $this->chartLine->getSprint());
    }

    public function sprintDataProvider()
    {
        return [
            [null],
            [$this->createMock(Sprint::class)],
            [new Sprint()],
        ];
    }

    public function testId(): void
    {
        $this->assertNull($this->chartLine->getId());
    }

    public function addRemoveChartPointDataProvider(): iterable
    {
        $chartLine   = new ChartLine();
        $chartPoint1 = new ChartPoint();
        $chartPoint2 = new ChartPoint();

        yield 'addRemoveChartPoint' => [
            $chartLine,
            $chartPoint1,
            $chartPoint2,
        ];
    }

    /**
     * @dataProvider addRemoveChartPointDataProvider
     */
    public function testAddRemoveChartPoint(ChartLine $chartLine)
    {
        $chartLine->addChartPoint($this->chartPoint1);
        $chartLine->addChartPoint($this->chartPoint2);

        $this->assertCount(2, $chartLine->getChartPoints());

        $chartLine->removeChartPoint($this->chartPoint2);
        $this->assertCount(1, $chartLine->getChartPoints());
        $this->assertFalse($chartLine->getChartPoints()->contains($this->chartPoint2));
    }

    public function setChartPointsDataProvider(): iterable
    {
        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        $chartLine1 = new ChartLine();
        $chartLine1->addChartPoint($chartPoint1);
        $chartLine1->addChartPoint($chartPoint2);
        $chartPoints1 = new ArrayCollection([$chartPoint1, $chartPoint2]);

        yield 'twoPoints' => [
            $chartLine1,
            $chartPoints1,
        ];

        $chartLine2   = new ChartLine();
        $chartPoints2 = new ArrayCollection([]);

        yield 'emptyPoints' => [
            $chartLine2,
            $chartPoints2,
        ];
    }

    /**
     * @dataProvider setChartPointsDataProvider
     */
    public function testSetChartPoints(ChartLine $chartLine, ArrayCollection $expectedChartPoints): void
    {
        $chartLine->setChartPoints($expectedChartPoints);

        $this->assertEquals($expectedChartPoints, $chartLine->getChartPoints());
    }

    public function removeChartPointsDataProvider(): iterable
    {
        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        $chartLine = new ChartLine();
        $chartLine->addChartPoint($chartPoint1);
        $chartLine->addChartPoint($chartPoint2);

        yield 'removeOnePoint' => [
            $chartLine,
            new ArrayCollection([$chartPoint1]),
            [$chartPoint2],
        ];
    }

    /**
     * @dataProvider removeChartPointsDataProvider
     */
    public function testRemoveChartPoints(
        ChartLine $chartLine,
        ArrayCollection $pointsToRemove,
        array $expectedRemainingPoints
    ): void {
        $chartLine->removeChartPoints($pointsToRemove);

        $this->assertCount(count($expectedRemainingPoints), $chartLine->getChartPoints());

        $remainingPointsArray = array_values($chartLine->getChartPoints()->toArray());

        foreach ($expectedRemainingPoints as $index => $expectedPoint) {
            $this->assertSame($expectedPoint, $remainingPointsArray[$index]);
        }
    }

    public function hasPointForDateDataProvider(): iterable
    {
        $chartLine   = new ChartLine();
        $chartPoint1 = new ChartPoint();
        $date1       = new DateTime('2024-06-01');
        $chartPoint1->setDate($date1);

        $chartPoint2 = new ChartPoint();
        $date2       = new DateTime('2024-06-02');
        $chartPoint2->setDate($date2);

        $chartLine->addChartPoint($chartPoint1);
        $chartLine->addChartPoint($chartPoint2);

        yield 'existingDates' => [
            $chartLine,
            $chartPoint1,
            $chartPoint2,
            new DateTime('2024-06-03'),
        ];
    }

    /**
     * @dataProvider hasPointForDateDataProvider
     */
    public function testHasPointForDate(
        ChartLine $chartLine,
        ChartPoint $chartPoint1,
        ChartPoint $chartPoint2,
        DateTime $nonExistentDate
    ) {
        $this->assertEquals($chartPoint1, $chartLine->hasPointForDate($chartPoint1->getDate()));
        $this->assertEquals($chartPoint2, $chartLine->hasPointForDate($chartPoint2->getDate()));

        $this->assertNull($chartLine->hasPointForDate($nonExistentDate));
    }

    public function toArrayDataProvider(): iterable
    {
        yield 'twoPoints' => [
            [
                'dates'  => [new DateTime('2024-06-01'), new DateTime('2024-06-02')],
                'values' => [10, 20],
            ],
            [
                'id'          => null,
                'chartPoints' => [
                    ['x' => '2024-06-01', 'y' => 10],
                    ['x' => '2024-06-02', 'y' => 20],
                ],
            ],
        ];
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(array $pointData, array $expectedArray): void
    {
        foreach ($pointData['dates'] as $index => $date) {
            $chartPoint = new ChartPoint();
            $chartPoint->setDate($date);
            $chartPoint->setValue($pointData['values'][$index]);
            $this->chartLine->addChartPoint($chartPoint);
        }

        $resultArray = $this->chartLine->toArray();

        $this->assertEquals($expectedArray, $resultArray);
    }

    public function fromArrayDataProvider(): iterable
    {
        yield 'twoPoints' => [
            [
                ['date' => '2024-06-01', 'value' => 10],
                ['date' => '2024-06-02', 'value' => 20],
            ],
            [
                'dates'  => [new DateTime('2024-06-01'), new DateTime('2024-06-02')],
                'values' => [10, 20],
            ],
        ];
    }

    /**
     * @dataProvider fromArrayDataProvider
     */
    public function testFromArray(array $data, array $expectedData): void
    {
        $chartLine = ChartLine::fromArray($data);

        $this->assertCount(count($data), $chartLine->getChartPoints());

        $chartPoints = $chartLine->getChartPoints()->toArray();

        foreach ($chartPoints as $index => $chartPoint) {
            $this->assertEquals($expectedData['dates'][$index], $chartPoint->getDate());
            $this->assertEquals($expectedData['values'][$index], $chartPoint->getValue());
        }
    }

    public function removePointsAfterDateDataProvider(): iterable
    {
        yield 'pointsAfterDate' => [
            [
                new DateTime('2024-06-01'),
                new DateTime('2024-06-02'),
            ],
            ['2024-06-01'],
        ];

        yield 'pointsBothAfterDate' => [
            [
                new DateTime('2024-06-02'),
                new DateTime('2024-06-03'),
            ],
            [],
        ];

        yield 'pointsBeforeDate' => [
            [
                new DateTime('2024-05-30'),
                new DateTime('2024-05-31'),
            ],
            ['2024-05-30', '2024-05-31'],
        ];
    }

    /**
     * @dataProvider removePointsAfterDateDataProvider
     */
    public function testRemovePointsAfterDate(array $pointDates, array $expectedDates): void
    {
        foreach ($pointDates as $date) {
            $chartPoint = new ChartPoint();
            $chartPoint->setDate($date);
            $this->chartLine->addChartPoint($chartPoint);
        }

        $this->chartLine->removePointsAfterDate(new DateTime('2024-06-01'));

        $this->assertCount(count($expectedDates), $this->chartLine->getChartPoints());

        foreach ($this->chartLine->getChartPoints() as $index => $point) {
            $this->assertEquals($expectedDates[$index], $point->getDate()->format('Y-m-d'));
        }
    }

    public function removePointsBeforeDateDataProvider(): iterable
    {
        yield 'pointsBeforeAndAfterDate' => [
            [
                'date1' => new DateTime('2024-06-01'),
                'date2' => new DateTime('2024-06-02'),
            ],
            ['2024-06-02'],
        ];

        yield 'pointsAfterDate' => [
            [
                'date1' => new DateTime('2024-06-02'),
                'date2' => new DateTime('2024-06-03'),
            ],
            ['2024-06-02', '2024-06-03'],
        ];

        yield 'pointsBeforeDate' => [
            [
                'date1' => new DateTime('2024-06-01'),
                'date2' => new DateTime('2024-06-01'),
            ],
            [],
        ];
    }

    /**
     * @dataProvider removePointsBeforeDateDataProvider
     */
    public function testRemovePointsBeforeDate(array $dates, array $expectedRemainingDates): void
    {
        $this->chartPoint1->setDate($dates['date1']);
        $this->chartPoint2->setDate($dates['date2']);

        $this->chartLine->addChartPoint($this->chartPoint1);
        $this->chartLine->addChartPoint($this->chartPoint2);

        $this->chartLine->removePointsBeforeDate(new DateTime('2024-06-02'));

        $remainingDates = array_map(
            fn($point) => $point->getDate()->format('Y-m-d'),
            $this->chartLine->getChartPoints()->toArray()
        );

        sort($remainingDates);

        $this->assertEquals($expectedRemainingDates, $remainingDates);
    }

    public function sortChartLineDataProvider(): iterable
    {
        yield 'unorderedDates' => [
            [
                'date1' => '2024-06-02',
                'date2' => '2024-06-01',
            ],
            ['2024-06-01', '2024-06-02'],
        ];

        yield 'orderedDates' => [
            [
                'date1' => '2024-06-01',
                'date2' => '2024-06-02',
            ],
            ['2024-06-01', '2024-06-02'],
        ];
    }

    /**
     * @dataProvider sortChartLineDataProvider
     * @throws Exception
     */
    public function testSortChartLine(array $dates, array $expectedSortedDates): void
    {
        $this->chartPoint1->setDate(new DateTime($dates['date1']));

        $this->chartPoint2->setDate(new DateTime($dates['date2']));

        $this->chartLine->addChartPoint($this->chartPoint1);
        $this->chartLine->addChartPoint($this->chartPoint2);

        $this->chartLine->sortChartLine();
        $sortedPoints = $this->chartLine->getChartPoints()->toArray();

        $sortedDates = array_map(
            fn($point) => $point->getDate()->format('Y-m-d'),
            $sortedPoints
        );

        sort($sortedDates);

        $this->assertEquals($expectedSortedDates, $sortedDates);
    }

    public function setCapacityDayChartPointsDataProvider(): iterable
    {
        $chartPoint1 = new CapacityDayChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new CapacityDayChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        $chartLine1 = new ChartLine();
        $chartLine1->addCapacityDayChartPoint($chartPoint1);
        $chartLine1->addCapacityDayChartPoint($chartPoint2);
        $chartPoints1 = new ArrayCollection([$chartPoint1, $chartPoint2]);

        yield 'twoPoints' => [
            $chartLine1,
            $chartPoints1,
        ];

        $chartLine2   = new ChartLine();
        $chartPoints2 = new ArrayCollection([]);

        yield 'emptyPoints' => [
            $chartLine2,
            $chartPoints2,
        ];
    }

    /**
     * @dataProvider setChartPointsDataProvider
     */
    public function testSetCapacityDayChartPoints(ChartLine $chartLine, ArrayCollection $expectedChartPoints): void
    {
        $chartLine->setCapacityDayChartPoints($expectedChartPoints);

        $this->assertEquals($expectedChartPoints, $chartLine->getCapacityDayChartPoints());
    }

    /**
     * @dataProvider removeChartPointsDataProvider
     */
    public function testRemoveCapacityDayChartPoints(
        ChartLine $chartLine,
        ArrayCollection $pointsToRemove
    ): void {
        $chartLine->removeCapacityDayChartPoints($pointsToRemove);

        $this->assertEmpty($chartLine->getCapacityDayChartPoints());
    }

    public function removeCapacityDayChartPointsDataProvider(): iterable
    {
        $chartPoint1 = new CapacityDayChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new CapacityDayChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        $chartLine = new ChartLine();
        $chartLine->addCapacityDayChartPoint($chartPoint1);
        $chartLine->addCapacityDayChartPoint($chartPoint2);

        yield 'removeTwoPoints' => [
            $chartLine,
            new ArrayCollection([$chartPoint1, $chartPoint2]),
        ];
    }

    /**
     * @dataProvider addCapacityDayChartPointsDataProvider
     */
    public function testAddCapacityDayChartPoint(
        ChartLine $chartLine,
        ArrayCollection $chartPoints
    ): void {
        foreach ($chartPoints as $chartPoint) {
            $chartLine->addCapacityDayChartPoint($chartPoint);
        }

        $this->assertCount(count($chartPoints), $chartLine->getChartPoints());

        foreach ($chartLine->getChartPoints()->toArray() as $index => $chartPoint) {
            $this->assertEquals($chartPoint, $chartPoints[$index]);
        }
    }

    public function addCapacityDayChartPointsDataProvider(): iterable
    {
        $chartLine   = new ChartLine();
        $chartPoint1 = new CapacityDayChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new CapacityDayChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        yield [
            $chartLine,
            new ArrayCollection([$chartPoint1, $chartPoint2]),
        ];
    }

    /**
     * @dataProvider isDayUpdatedDataProvider
     */
    public function testIsDayUpdated(array $updatedDays, DateTime $date, bool $expected)
    {
        $this->chartLine->setCapacityDayChartPoints(new ArrayCollection($updatedDays));

        $this->assertEquals($expected, !!$this->chartLine->isDayUpdated($date));
    }

    public function isDayUpdatedDataProvider(): iterable
    {
        $chartPoint1 = new CapacityDayChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-01'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new CapacityDayChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-02'));
        $chartPoint2->setValue(20);

        return [
            'dayUpdated'    => [
                [$chartPoint1, $chartPoint2],
                new DateTime('2024-06-01'),
                true,
            ],
            'dayNotUpdated' => [
                [$chartPoint1, $chartPoint2],
                new DateTime('2024-06-03'),
                false,
            ],
        ];
    }

    public function testGetSortedArrayByDate(ChartLine $chartLine, array $expectedArray): void
    {
        $this->assertEquals($expectedArray, $chartLine->getSortedArrayByDate());
    }

    public function getSortedArrayByDateDataProvider(): iterable
    {
        $chartLine = new ChartLine();

        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-06-02'));
        $chartPoint1->setValue(20);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-06-01'));
        $chartPoint2->setValue(10);

        $chartLine->addChartPoint($chartPoint1);
        $chartLine->addChartPoint($chartPoint2);

        yield [
            $chartLine,
            'id' => null,
            'chartPoints' => [
                ['x' => '2024-06-01', 'y' => 10],
                ['x' => '2024-06-02', 'y' => 20],
            ],
        ];
    }
}
