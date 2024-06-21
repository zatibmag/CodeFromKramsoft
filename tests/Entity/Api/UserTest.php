<?php

namespace App\Tests\Entity\Api;

use App\Entity\Api\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public static function getUsernameDataProvider(): iterable
    {
        yield ['Test'];
        yield ['AnotherUsername'];
    }

    /**
     * @dataProvider getUsernameDataProvider
     */
    public function testName(string $username): void
    {
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testId(): void
    {
        $this->assertNull($this->user->getId());
    }

    public static function getRolesDataProvider(): iterable
    {
        yield [
            ['ROLE_ADMIN', 'ROLE_USER'],
            ['ROLE_ADMIN', 'ROLE_USER'],
        ];

        yield [
            ['ROLE_USER'],
            ['ROLE_USER'],
        ];

        yield [
            ['ROLE_GUEST'],
            ['ROLE_GUEST'],
        ];

        yield [
            ['ROLE_USER', 'ROLE_GUEST'],
            ['ROLE_USER', 'ROLE_GUEST'],
        ];

        yield [
            [],
            [],
        ];
    }

    /**
     * @dataProvider getRolesDataProvider
     */
    public function testRoles(array $roles, array $expectedRoles): void
    {
        $this->user->setRoles($roles);
        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    public static function getPasswordDataProvider(): iterable
    {
        yield ['password'];
        yield ['another_password'];
    }

    /**
     * @dataProvider getPasswordDataProvider
     */
    public function testPassword(string $password): void
    {
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    /**
     * @dataProvider getUsernameDataProvider
     */
    public function testGetUserIdentifier(string $username): void
    {
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUserIdentifier());
    }

    public function createFromArrayDataProvider(): iterable
    {
        yield [
            [
                'username' => 'TestUser1',
                'password' => 'test_password1',
                'roles'    => ['ROLE_ADMIN', 'ROLE_USER'],
            ],
        ];

        yield [
            [
                'username' => 'TestUser2',
                'password' => 'test_password2',
                'roles'    => ['ROLE_USER'],
            ],
        ];
    }

    /**
     * @dataProvider createFromArrayDataProvider
     */
    public function testCreateFromArray(array $params): void
    {
        $user = User::fromArray($params);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($params['username'], $user->getUsername());
        $this->assertEquals($params['password'], $user->getPassword());
        $this->assertEquals($params['roles'], $user->getRoles());
    }

    public function toArrayDataProvider(): iterable
    {
        yield [
            'username'      => 'TestUser1',
            'roles'         => ['ROLE_ADMIN', 'ROLE_USER'],
            'expectedArray' => [
                'id'       => null,
                'username' => 'TestUser1',
                'roles'    => ['ROLE_ADMIN', 'ROLE_USER'],
            ],
        ];

        yield [
            'username'      => 'TestUser2',
            'roles'         => ['ROLE_USER'],
            'expectedArray' => [
                'id'       => null,
                'username' => 'TestUser2',
                'roles'    => ['ROLE_USER'],
            ],
        ];
    }

    /**
     * @dataProvider toArrayDataProvider
     */
    public function testToArray(string $username, array $roles, array $expectedArray): void
    {
        $user = new User();

        $user->setUsername($username);
        $user->setRoles($roles);

        sort($expectedArray['roles']);
        sort($user->toArray()['roles']);

        $this->assertEquals($expectedArray, $user->toArray());
    }
}
