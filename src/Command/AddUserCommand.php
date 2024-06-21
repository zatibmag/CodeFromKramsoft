<?php

namespace App\Command;

use App\Api\Manager\PermissionManager;
use App\Executor\AddUserExecutor;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AddUserCommand
 *
 * @package App\Command
 */
class AddUserCommand extends Command
{
    protected static $defaultName        = 'app:add-user';
    protected static $defaultDescription = 'Adds a new user.';

    private AddUserExecutor             $addUserExecutor;
    private UserPasswordHasherInterface $passwordHasher;
    private PermissionManager           $permissionManager;

    public function __construct(
        AddUserExecutor $addUserExecutor,
        UserPasswordHasherInterface $passwordHasher,
        PermissionManager $permissionManager
    ) {
        parent::__construct();
        $this->addUserExecutor   = $addUserExecutor;
        $this->passwordHasher    = $passwordHasher;
        $this->permissionManager = $permissionManager;
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->addOption('--role', null, InputOption::VALUE_REQUIRED)
            ->setHelp('The <info>app:add-user</info> command adds a new user.')
            ->addUsage('Usage: <info>app:add-user [--scrum-master | --team-member | --admin]</info>');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $roles = $this->permissionManager->identifyRole($input);

            return $this->addUserExecutor->execute($input, $output, $this->passwordHasher, $roles);
        } catch (InvalidArgumentException $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }
    }
}
