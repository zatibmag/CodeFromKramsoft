<?php

namespace App\Tests\Api\Manager;

use PHPUnit\Framework\TestCase;
use App\Api\Manager\PermissionManager;
use Symfony\Component\Console\Input\InputInterface;
use InvalidArgumentException;

class PermissionManagerTest extends TestCase
{
    private PermissionManager $permissionManager;

    protected function setUp(): void
    {
        $this->permissionManager = new PermissionManager();
    }

    /**
     * @dataProvider validRoleProvider
     */
    public function testIdentifyRoleValid($inputRole, $expectedRole)
    {
        $inputInterface = $this->createMock(InputInterface::class);
        $inputInterface->method('getOption')->willReturn($inputRole);

        $this->assertEquals([$expectedRole], $this->permissionManager->identifyRole($inputInterface));
    }

    /**
     * @dataProvider invalidRoleProvider
     */
    public function testIdentifyRoleInvalid($inputRole)
    {
        $this->expectException(InvalidArgumentException::class);

        $inputInterface = $this->createMock(InputInterface::class);
        $inputInterface->method('getOption')->willReturn($inputRole);

        $this->permissionManager->identifyRole($inputInterface);
    }

    // Data Providers

    public function validRoleProvider()
    {
        return [
            ['super-admin', 'ROLE_SUPER_ADMIN'],
            ['admin', 'ROLE_ADMIN'],
            ['scrum-master', 'ROLE_SCRUM_MASTER'],
            ['team-member', 'ROLE_TEAM_MEMBER'],
        ];
    }

    public function invalidRoleProvider()
    {
        return [
            ['invalid-role'],
            ['random-role'],
            [''],
            [null],
        ];
    }
}
