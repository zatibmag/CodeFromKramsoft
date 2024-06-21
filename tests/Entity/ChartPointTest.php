<?php

namespace App\Tests\Entity;

use App\Entity\ChartPoint;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class ChartPointTest extends TestCase
{
    protected DateTime   $date;
    protected int        $value;
    protected ChartPoint $chartPoint;

    protected function setUp(): void
    {
        $this->date       = new DateTime('2024-04-15');
        $this->value      = 10;
        $this->chartPoint = new ChartPoint();
        $this->chartPoint->setValue($this->value);
        $this->chartPoint->setDate($this->date);
    }

    public function testId(): void
    {
        $this->assertNull($this->chartPoint->getId());
    }

    public function testValue(): void
    {
        $this->assertEquals($this->value, $this->chartPoint->getValue());

        $value = 10;
        $this->chartPoint->setValue($value);
        $this->assertEquals($value, $this->chartPoint->getValue());
    }

    public function testDate(): void
    {
        $this->assertEquals($this->date, $this->chartPoint->getDate());

        $date = new DateTime('2025-05-20');
        $this->chartPoint->setDate($date);
        $this->assertEquals($date, $this->chartPoint->getDate());
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(DateTime $date, int $value, array $expectedArray): void
    {
        $chartPoint = new ChartPoint();
        $chartPoint->setValue($value);
        $chartPoint->setDate($date);
        $this->assertEquals($expectedArray, $chartPoint->toArray());
    }

    public function toArrayDataProvider(): iterable
    {
        yield [
            new DateTime('2024-04-15'),
            10,
            [
                "x" => '2024-04-15',
                "y" => 10,
            ],
        ];
    }

    /**
     * @dataProvider fromArrayDataProvider
     * @throws Exception
     */
    public function testFromArray(DateTime $date, int $value, array $data): void
    {
        $chartPoint = ChartPoint::fromArray($data);
        $this->assertInstanceOf(ChartPoint::class, $chartPoint);
        $this->assertEquals($date->format('Y-m-d H:i:s'), $chartPoint->getDate()->format('Y-m-d H:i:s'));
        $this->assertEquals($value, $chartPoint->getValue());
    }

    public function fromArrayDataProvider(): iterable
    {
        yield [
            new DateTime('2024-04-15'),
            10,
            ['date' => '2024-04-15', 'value' => 10],
        ];
    }
}
