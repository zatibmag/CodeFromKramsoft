<?php

namespace App\Tests\Executor;

use App\Entity\Api\User;
use App\Executor\AddUserExecutor;
use App\Manager\UserQuestionManager;
use App\Serializer\Api\ArrayConvertibleDataSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class AddUserExecutorTest extends TestCase
{
    /**
     * @dataProvider userDataProvider
     * @throws Exception
     */
    public function testExecute(array $userData): void
    {
        $inputMock  = new ArrayInput([]);
        $outputMock = new NullOutput();
        $ioMock     = new SymfonyStyle($inputMock, $outputMock);

        $questionManagerMock = $this->createMock(UserQuestionManager::class);
        $serializerMock      = $this->createMock(ArrayConvertibleDataSerializer::class);
        $entityManagerMock   = $this->createMock(EntityManagerInterface::class);

        $passwordHasherFactoryMock = $this->createMock(PasswordHasherFactoryInterface::class);

        $passwordHasher = new UserPasswordHasher($passwordHasherFactoryMock);

        $executor = new AddUserExecutor(
            $questionManagerMock,
            $entityManagerMock,
            $serializerMock
        );

        $questionManagerMock->expects($this->once())
            ->method('manage')
            ->willReturn($userData);

        $user = new User();
        $serializerMock->expects($this->once())
            ->method('deserialize')
            ->willReturn($user);

        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($user);

        $entityManagerMock->expects($this->once())
            ->method('flush');

        $result = $executor->execute($inputMock, $ioMock, $passwordHasher);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public static function userDataProvider(): iterable
    {
        yield [
            ['name' => 'test', 'password' => 'test123'],
            ['name' => 'user', 'password' => 'password123'],
        ];
    }

}
