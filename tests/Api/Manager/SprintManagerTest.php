<?php

namespace Api\Manager;

use App\Api\Manager\SprintManager;
use App\Entity\Api\Sprint;
use App\Repository\Api\SprintRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SprintManagerTest extends TestCase
{
    private SprintManager    $sprintManager;
    private SprintRepository $sprintRepository;

    protected function setUp(): void
    {
        $this->sprintRepository = $this->createMock(SprintRepository::class);
        $this->sprintManager    = new SprintManager($this->sprintRepository);
    }

    /**
     * @dataProvider sprintDataProvider
     */
    public function testGetSprintByIdOrCurrent($sprintId, $expectedSprint, $expectException)
    {
        if ($sprintId !== null) {
            $this->sprintRepository->expects($this->once())
                ->method('find')
                ->with($sprintId)
                ->willReturn($expectedSprint);
        } else {
            $this->sprintRepository->expects($this->once())
                ->method('getCurrentSprint')
                ->willReturn($expectedSprint);
        }

        if ($expectException) {
            $this->expectException(NotFoundHttpException::class);
            $this->expectExceptionMessage('Sprint not found');
        }

        $result = $this->sprintManager->getSprintByIdOrCurrent($sprintId);

        $this->assertSame($expectedSprint, $result);
    }

    public function sprintDataProvider()
    {
        return [
            [1, new Sprint(), false],
            [null, new Sprint(), false],
            [null, null, true],
        ];
    }
}
