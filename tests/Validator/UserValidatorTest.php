<?php

namespace App\Tests\Validator;

use App\Repository\Api\UserRepository;
use App\Validator\UserValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase
{
    private UserValidator  $validator;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->validator      = new UserValidator($this->userRepository);
    }

    /**
     * @doesNotPerformAssertions
     */
    public static function testNameValidationDataProvider(): iterable
    {
        yield [null, InvalidArgumentException::class, null];
        yield ['', InvalidArgumentException::class, null];
        yield ['         ', InvalidArgumentException::class, null];
        yield ['user', null, false];
    }


    /**
     * @dataProvider testNameValidationDataProvider
     */
    public function testNameValidation(?string $value, ?string $expectedExceptionClassName, ?bool $userValue): void
    {
        $this->userRepository->expects($userValue !== null ? self::once() : self::never())->method('findOneBy');

        if ($expectedExceptionClassName !== null) {
            $this->expectException($expectedExceptionClassName);
        }

        $this->assertEquals($expectedExceptionClassName === null, $this->validator->usernameValidate($value));
    }

    public function testExistingUserNameValidation(): void
    {
        $existingName = 'existing_user';
        $this->userRepository->method('findOneBy')->willReturn(['username' => $existingName]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User with this username already exists');

        $this->validator->usernameValidate($existingName);
    }

    public static function passwordValidationDataProvider(): iterable
    {
        yield 'null_value' => [null, 'Value is empty'];
        yield 'empty_string' => ['', 'Value is empty'];
        yield 'whitespace_string' => ['    ', 'Value is empty'];
        yield 'valid_password' => ['validpassword', ''];
    }


    /**
     * @dataProvider passwordValidationDataProvider
     */
    public function testPasswordValidation($password, $expectedExceptionMessage): void
    {
        empty($expectedExceptionMessage) ?: $this->expectExceptionMessage($expectedExceptionMessage);

        $this->assertSame(
            $expectedExceptionMessage === '',
            $this->validator->passwordValidate($password)
        );
    }
}
