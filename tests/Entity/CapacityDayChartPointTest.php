<?php

namespace App\Tests\Entity;

use App\Entity\CapacityDayChartPoint;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class CapacityDayChartPointTest extends TestCase
{

    /**
     * @dataProvider fromArrayDataProvider
     * @throws Exception
     */
    public function testFromArray(DateTime $date, int $value, array $data): void
    {
        $chartPoint = CapacityDayChartPoint::fromArray($data);
        $this->assertInstanceOf(CapacityDayChartPoint::class, $chartPoint);
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
