<?php

namespace App\Tests\Entity\Api;

use App\Entity\Api\Sprint;
use App\Entity\Api\SprintExcludedDay;
use App\Entity\Api\SprintStory;
use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class SprintTest extends TestCase
{
    private Sprint $sprint;

    protected function setUp(): void
    {
        $this->sprint = new Sprint();
    }

    /**
     * @dataProvider chartLinesDataProvider
     */
    public function testSetAndGetChartLines($chartLines)
    {
        $this->sprint->setChartLines($chartLines);
        $this->assertEquals($chartLines, $this->sprint->getChartLines());
    }

    public function chartLinesDataProvider()
    {
        return [
            [$this->createMock(ArrayCollection::class)],
            [new ArrayCollection()],
            [new ArrayCollection([new ChartLine(), new ChartLine()])],
            [new ArrayCollection()],
        ];
    }

    /**
     * @dataProvider chartLineDataProvider
     */
    public function testAddAndRemoveChartLine($chartLine)
    {
        $this->sprint->addChartLine($chartLine);
        $this->assertCount(1, $this->sprint->getChartLines());

        $this->sprint->removeChartLine($chartLine);
        $this->assertCount(0, $this->sprint->getChartLines());
    }

    public function chartLineDataProvider()
    {
        return [
            [$this->createMock(ChartLine::class)],
            [new ChartLine()],
        ];
    }

    public function testId(): void
    {
        $this->assertNull($this->sprint->getId());
    }

    public function testName(): void
    {
        $this->assertNull($this->sprint->getName());

        $this->sprint->setName($expected = 'name');
        $this->assertEquals($expected, $this->sprint->getName());
    }

    public function testNumberOfTasks(): void
    {
        $this->assertNull($this->sprint->getCapacity());

        $this->sprint->setCapacity($expected = 10);
        $this->assertEquals($expected, $this->sprint->getCapacity());
    }

    public function testStartAt(): void
    {
        $this->assertNull($this->sprint->getStartAt());

        $startAt = new DateTime('2024-04-15');
        $this->sprint->setStartAt($startAt);
        $this->assertEquals($startAt, $this->sprint->getStartAt());
    }

    public function testEndAt(): void
    {
        $this->assertNull($this->sprint->getEndAt());

        $endAt = new DateTime('2024-04-20');
        $this->sprint->setEndAt($endAt);
        $this->assertEquals($endAt, $this->sprint->getEndAt());
    }

    /**
     * @dataProvider sprintStoriesProvider
     */
    public function testSprintStories(array $values, array $expected)
    {
        self::assertInstanceof(ArrayCollection::class, $this->sprint->getSprintStories());
        self::assertEmpty($this->sprint->getSprintStories());

        $this->sprint->setSprintStories(new ArrayCollection($values));
        self::assertSame($expected, $this->sprint->getSprintStories()->toArray());
    }

    public function sprintStoriesProvider(): iterable
    {
        $sprintStory1 = new SprintStory();
        $sprintStory2 = new SprintStory();

        yield 'empty' => [[], []];
        yield 'one' => [[$sprintStory1], [$sprintStory1]];
        yield 'two' => [[$sprintStory1, $sprintStory2], [$sprintStory1, $sprintStory2]];
    }

    /**
     * @dataProvider addingSprintStoriesProvider
     */
    public function testAddingSprintStories(array $initialValues, array $values, array $expected)
    {
        $this->sprint->setSprintStories(new ArrayCollection($initialValues));

        foreach ($values as $value) {
            $this->sprint->addSprintStory($value);
        }

        self::assertSame($expected, $this->sprint->getSprintStories()->toArray());
    }

    public function addingSprintStoriesProvider(): iterable
    {
        $sprintStory1 = new SprintStory();
        $sprintStory2 = new SprintStory();

        yield 'add to empty list' => [[], [$sprintStory1], [$sprintStory1]];
        yield 'add to not empty list' => [[$sprintStory1], [$sprintStory2], [$sprintStory1, $sprintStory2]];
    }

    /**
     * @dataProvider removingSprintStoriesProvider
     */
    public function testRemovingSprintStories(array $initialValues, array $values, array $expected)
    {
        $this->sprint->setSprintStories(new ArrayCollection($initialValues));

        foreach ($values as $value) {
            $this->sprint->removeSprintStory($value);
        }

        self::assertSame($expected, $this->sprint->getSprintStories()->toArray());
    }

    public function removingSprintStoriesProvider(): iterable
    {
        $sprintStory1 = new SprintStory();
        $sprintStory2 = new SprintStory();

        yield 'remove from list' => [[$sprintStory1, $sprintStory2], [$sprintStory2], [$sprintStory1]];
        yield 'remove from empty list' => [[], [$sprintStory1], []];
        yield 'remove not existing' => [[$sprintStory1], [$sprintStory2], [$sprintStory1]];
    }

    /**
     * @dataProvider fromArrayDataProvider
     */
    public function testFromArray(array $data, array $expectedData): void
    {
        $sprint = Sprint::fromArray($data);

        $this->assertInstanceOf(Sprint::class, $sprint);
        $this->assertEquals($expectedData['name'], $sprint->getName());
        $this->assertEquals($expectedData['capacity'], $sprint->getCapacity());
        $this->assertEquals($expectedData['startAt'], $sprint->getStartAt());
        $this->assertEquals($expectedData['endAt'], $sprint->getEndAt());
        $this->assertEquals($expectedData['capacityType'], $sprint->getCapacityType());

        $this->assertCount(count($expectedData['sprintStories']), $sprint->getSprintStories());
        foreach ($sprint->getSprintStories() as $index => $sprintStory) {
            $this->assertEquals($expectedData['sprintStories'][$index]->getCapacity(), $sprintStory->getCapacity());
        }

        $this->assertCount(count($expectedData['excludedDays']), $sprint->getExcludedDays());
        foreach ($sprint->getExcludedDays() as $index => $excludedDay) {
            $this->assertEquals($expectedData['excludedDays'][$index]->getDate(), $excludedDay->getDate());
        }
    }

    public function fromArrayDataProvider(): array
    {
        $sprintStory1 = new SprintStory();
        $sprintStory1->setCapacity(10);

        $sprintStory2 = new SprintStory();
        $sprintStory2->setCapacity(20);

        $excludedDay1 = new SprintExcludedDay();
        $excludedDay1->setDate(new DateTime('2024-04-16'));

        $excludedDay2 = new SprintExcludedDay();
        $excludedDay2->setDate(new DateTime('2024-04-17'));

        $chartPoint1 = new ChartPoint();
        $chartPoint1->setDate(new DateTime('2024-04-15'));
        $chartPoint1->setValue(10);

        $chartPoint2 = new ChartPoint();
        $chartPoint2->setDate(new DateTime('2024-04-16'));
        $chartPoint2->setValue(20);

        $perfectChartLine = new ChartLine();
        $perfectChartLine->addChartPoint($chartPoint1);
        $perfectChartLine->addChartPoint($chartPoint2);

        $currentChartLine = new ChartLine();
        $currentChartLine->addChartPoint($chartPoint1);
        $currentChartLine->addChartPoint($chartPoint2);

        return [
            'test case 1' => [
                'data'         => [
                    'name'             => 'Test Sprint',
                    'capacity'         => 20,
                    'startAt'          => new DateTime('2024-04-15'),
                    'endAt'            => new DateTime('2024-04-20'),
                    'sprintStories'    => new ArrayCollection([$sprintStory1, $sprintStory2]),
                    'capacityType'     => 1,
                    'excludedDays'     => new ArrayCollection([$excludedDay1, $excludedDay2]),
                ],
                'expectedData' => [
                    'name'             => 'Test Sprint',
                    'capacity'         => 20,
                    'startAt'          => new DateTime('2024-04-15'),
                    'endAt'            => new DateTime('2024-04-20'),
                    'capacityType'     => 1,
                    'sprintStories'    => [$sprintStory1, $sprintStory2],
                    'excludedDays'     => [$excludedDay1, $excludedDay2],
                ],
            ],
        ];
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(
        string $name,
        int $capacity,
        DateTime $startAt,
        DateTime $endAt,
        ArrayCollection $sprintStories,
        ArrayCollection $excludedDays,
        array $expectedArray
    ): void {
        $this->sprint->setName($name);
        $this->sprint->setCapacity($capacity);
        $this->sprint->setStartAt($startAt);
        $this->sprint->setEndAt($endAt);
        $this->sprint->setListDoneId('1');

        $this->sprint->setSprintStories($sprintStories);
        $this->sprint->setExcludedDays($excludedDays);

        $perfectChartLine = new ChartLine();
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(10));
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(20));
        $this->sprint->setPerfectChartLine($perfectChartLine);

        $currentChartLine = new ChartLine();
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(15));
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(25));
        $this->sprint->setCurrentChartLine($currentChartLine);

        $this->assertEquals($expectedArray, $this->sprint->toArray());
    }

    public function toArrayDataProvider(): iterable
    {
        $sprintStory1 = new SprintStory();
        $sprintStory1->setCapacity(10);

        $sprintStory2 = new SprintStory();
        $sprintStory2->setCapacity(10);

        $excludedDay1 = new SprintExcludedDay();
        $excludedDay1->setDate(new DateTime('2024-04-16'));

        $excludedDay2 = new SprintExcludedDay();
        $excludedDay2->setDate(new DateTime('2024-04-17'));

        $perfectChartLine = new ChartLine();
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(10));
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(20));

        $currentChartLine = new ChartLine();
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(15));
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(25));

        yield [
            'Test Sprint',
            20,
            new DateTime('2024-04-15'),
            new DateTime('2024-04-20'),
            new ArrayCollection([$sprintStory1, $sprintStory2]),
            new ArrayCollection([$excludedDay1, $excludedDay2]),
            [
                'id'               => null,
                'name'             => 'Test Sprint',
                'capacity'         => 20,
                'startAt'          => '2024-04-15',
                'endAt'            => '2024-04-20',
                'sprintStories'    => [
                    $sprintStory1->toArray(),
                    $sprintStory2->toArray(),
                ],
                'listDoneId'       => '1',
                'capacityType'     => null,
                'excludedDays'     => [
                    $excludedDay1->toArray(),
                    $excludedDay2->toArray(),
                ],
            ],
        ];
    }

    public function testListDoneId(): void
    {
        $this->assertNull($this->sprint->getListDoneId());

        $this->sprint->setListDoneId($expected = 1);
        $this->assertEquals($expected, $this->sprint->getListDoneId());
    }

    public function testCapacityType(): void
    {
        $this->assertNull($this->sprint->getCapacityType());

        $this->sprint->setCapacityType($expected = 1);
        $this->assertEquals($expected, $this->sprint->getCapacityType());
    }

    public function testBoardId(): void
    {
        $this->assertNull($this->sprint->getBoardId());

        $this->sprint->setBoardId($expected = 1);
        $this->assertEquals($expected, $this->sprint->getBoardId());
    }

    /**
     * @dataProvider excludedDaysProvider
     */
    public function testExcludedDays(array $values, array $expected)
    {
        self::assertInstanceof(ArrayCollection::class, $this->sprint->getExcludedDays());
        self::assertEmpty($this->sprint->getExcludedDays());

        $this->sprint->setExcludedDays(new ArrayCollection($values));
        self::assertSame($expected, $this->sprint->getExcludedDays()->toArray());
    }

    public function excludedDaysProvider(): iterable
    {
        $excludedDay1 = new SprintExcludedDay();
        $excludedDay2 = new SprintExcludedDay();

        yield 'empty' => [[], []];
        yield 'one' => [[$excludedDay1], [$excludedDay1]];
        yield 'two' => [[$excludedDay1, $excludedDay2], [$excludedDay1, $excludedDay2]];
    }

    /**
     * @dataProvider addingExcludedDaysProvider
     */
    public function testAddingExcludedDays(array $initialValues, array $values, array $expected)
    {
        $this->sprint->setExcludedDays(new ArrayCollection($initialValues));

        foreach ($values as $value) {
            $this->sprint->addExcludedDay($value);
        }

        self::assertSame($expected, $this->sprint->getExcludedDays()->toArray());
    }

    public function addingExcludedDaysProvider(): iterable
    {
        $excludedDay1 = new SprintExcludedDay();
        $excludedDay2 = new SprintExcludedDay();

        yield 'add to empty list' => [[], [$excludedDay1], [$excludedDay1]];
        yield 'add to not empty list' => [[$excludedDay1], [$excludedDay2], [$excludedDay1, $excludedDay2]];
    }

    /**
     * @dataProvider removingExcludedDaysProvider
     */
    public function testRemovingExcludedDays(array $initialValues, array $values, array $expected)
    {
        $this->sprint->setExcludedDays(new ArrayCollection($initialValues));

        foreach ($values as $value) {
            $this->sprint->removeExcludedDay($value);
        }

        self::assertSame($expected, $this->sprint->getExcludedDays()->toArray());
    }

    public function removingExcludedDaysProvider(): iterable
    {
        $excludedDay1 = new SprintExcludedDay();
        $excludedDay2 = new SprintExcludedDay();

        yield 'remove from list' => [[$excludedDay1, $excludedDay2], [$excludedDay2], [$excludedDay1]];
        yield 'remove from empty list' => [[], [$excludedDay1], []];
        yield 'remove not existing' => [[$excludedDay1], [$excludedDay2], [$excludedDay1]];
    }

    /**
     * @dataProvider isDaysExcludedDataProvider
     */
    public function testIsDayExcluded($excludedDays, $date, $expected)
    {
        $this->sprint->setExcludedDays(new ArrayCollection($excludedDays));

        $this->assertEquals($expected, $this->sprint->isDayExcluded($date));
    }

    public function isDaysExcludedDataProvider(): iterable
    {
        $excludedDay1 = new SprintExcludedDay();
        $excludedDay1->setDate(new DateTime('2024-04-16'));

        $excludedDay2 = new SprintExcludedDay();
        $excludedDay2->setDate(new DateTime('2024-04-17'));

        return [
            'day_excluded'     => [
                [$excludedDay1, $excludedDay2],
                new DateTime('2024-04-16'),
                true,
            ],
            'day_not_excluded' => [
                [$excludedDay1, $excludedDay2],
                new DateTime('2024-04-15'),
                false,
            ],
        ];
    }

    /**
     * @dataProvider perfectChartLineDataProvider
     */
    public function testSetAndGetPerfectChartLine(
        ChartLine $perfectChartLine
    ): void
    {
        $this->sprint->setPerfectChartLine($perfectChartLine);
        $this->assertEquals($perfectChartLine,  $this->sprint->getPerfectChartLine());
    }

    public function perfectChartLineDataProvider(): iterable
    {
        $perfectChartLine = new ChartLine();
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(10));
        $perfectChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(20));

        yield [
            $perfectChartLine
        ];
    }

    /**
     * @dataProvider currentChartLineDataProvider
     */
    public function testSetAndGetCurrentChartLine(
        ChartLine $currentChartLine
    ): void
    {
        $this->sprint->setCurrentChartLine($currentChartLine);
        $this->assertEquals($currentChartLine,  $this->sprint->getCurrentChartLine());
    }

    public function currentChartLineDataProvider(): iterable
    {
        $currentChartLine = new ChartLine();
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-15'))->setValue(10));
        $currentChartLine->addChartPoint((new ChartPoint())->setDate(new DateTime('2024-04-16'))->setValue(20));

        yield [
            $currentChartLine
        ];
    }

}
