<?php

namespace App\Executor;

use App\Entity\Api\User;
use App\Manager\UserQuestionManager;
use App\Serializer\Api\ArrayConvertibleDataSerializer;
use App\Serializer\UserSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddUserExecutor
{
    private UserQuestionManager    $userQuestionManager;
    private EntityManagerInterface         $entityManager;
    private ArrayConvertibleDataSerializer $userSerializer;

    public function __construct(
        UserQuestionManager $userQuestionManager,
        EntityManagerInterface $entityManager,
        ArrayConvertibleDataSerializer $userSerializer
    ) {
        $this->userQuestionManager = $userQuestionManager;
        $this->entityManager       = $entityManager;
        $this->userSerializer      = $userSerializer;
    }

    /**
     * @throws Exception
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output,
        UserPasswordHasherInterface $passwordHasher
    ): int {
        $io = new SymfonyStyle($input, $output);

        $params = $this->userQuestionManager->manage($input, $output);

        $user = $this->userSerializer->deserialize($params, User::class, 'json');

        $hashedPassword = $passwordHasher->hashPassword($user, $params['password']);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('User has been successfully created.');

        return Command::SUCCESS;
    }
}
