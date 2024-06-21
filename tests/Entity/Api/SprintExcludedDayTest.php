<?php

namespace App\Tests\Entity\Api;

use App\Entity\Api\Sprint;
use App\Entity\Api\SprintExcludedDay;
use DateTime;
use PHPUnit\Framework\TestCase;

class SprintExcludedDayTest extends TestCase
{
    private SprintExcludedDay $sprintExcludedDay;
    private Sprint            $sprint;

    protected function setUp(): void
    {
        $this->sprintExcludedDay = new SprintExcludedDay();
        $this->sprint = new Sprint();
    }

    public function testId(): void
    {
        $this->assertNull($this->sprintExcludedDay->getId());
    }

    public function testDate(): void
    {
        $this->assertNull($this->sprintExcludedDay->getDate());
        $this->sprintExcludedDay->setDate($expected = new DateTime());
        $this->assertEquals($expected, $this->sprintExcludedDay->getDate());
    }

    public function testSprint(): void
    {
        $this->assertNull($this->sprintExcludedDay->getSprint());

        $this->sprintExcludedDay->setSprint($expected = $this->sprint);
        $this->assertEquals($expected, $this->sprintExcludedDay->getSprint());
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(\DateTimeInterface $date, array $expectedArray): void {
        $this->sprintExcludedDay->setDate($date);
        $this->assertEquals($expectedArray, $this->sprintExcludedDay->toArray());
    }

    public function toArrayDataProvider(): iterable {
        yield [
            new DateTime('2024-05-23 14:30:00'),
            [
                'date' => '2024-05-23 14:30:00'
            ]
        ];
    }
}
