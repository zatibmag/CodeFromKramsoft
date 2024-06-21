<?php

namespace App\Tests\Entity\Api;

use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use DateTime;
use PHPUnit\Framework\TestCase;

class SprintStoryTest extends TestCase
{
    private SprintStory $sprintStory;
    private Sprint      $sprint;

    protected function setUp(): void
    {
        $this->sprintStory = new SprintStory();
        $this->sprint      = new Sprint();
    }

    public function testId(): void
    {
        $this->assertNull($this->sprintStory->getId());
    }

    public function testCreatedAt(): void
    {
        $createdAt = new DateTime();

        $createdAtFromSprintStory = $this->sprintStory->getCreatedAt();

        $expected = $createdAt->format('Y-m-d\TH:i:s');
        $actual   = $createdAtFromSprintStory->format('Y-m-d\TH:i:s');

        $this->assertEquals($expected, $actual);
    }

    public function testValue(): void
    {
        $this->assertNull($this->sprintStory->getCapacity());

        $this->sprintStory->setCapacity($expected = 10);
        $this->assertEquals($expected, $this->sprintStory->getCapacity());
    }

    public function testSprint(): void
    {
        $this->assertNull($this->sprintStory->getSprint());

        $this->sprintStory->setSprint($expected = $this->sprint);
        $this->assertEquals($expected, $this->sprintStory->getSprint());
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(int $capacity): void
    {
        $expectedPartOfCreatedAt = new DateTime();
        $sprintStory = new SprintStory();

        $sprintStory->setCapacity($capacity);

        $resultArray = $sprintStory->toArray();

        self::assertIsString($resultArray['createdAt']);
        self::assertStringStartsWith($expectedPartOfCreatedAt->format('Y-m-d H:i'), $resultArray['createdAt']);

        self::assertEquals($capacity, $resultArray['capacity']);
    }

    public function toArrayDataProvider(): iterable
    {
        yield [
            'value' => 10,
        ];
    }
}
