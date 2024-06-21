<?php

namespace App\Manager;

use App\Api\Manager\PermissionManager;
use App\Validator\UserValidator;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UserQuestionManager
{
    private QuestionHelper    $helper;
    private UserValidator     $userValidator;
    private PermissionManager $roleOptionCheck;

    public function __construct(
        UserValidator $userValidator,
        PermissionManager $roleOptionCheck,
        QuestionHelper $helper = null
    ) {
        $this->userValidator   = $userValidator;
        $this->roleOptionCheck = $roleOptionCheck;
        $this->helper          = $helper ?? new QuestionHelper();
    }

    public function manage(InputInterface $input, OutputInterface $output): array
    {
        $username = $this->askValidName($input, $output);
        $password = $this->askValidPassword($input, $output);
        $roles    = $this->roleOptionCheck->identifyRole($input);

        return [
            'username' => $username,
            'password' => $password,
            'roles'    => $roles,
        ];
    }

    private function askValidName(InputInterface $input, OutputInterface $output): string
    {
        do {
            $username = $this->helper->ask(
                $input,
                $output,
                new Question('Please enter the username of the new user: ')
            );

            try {
                $isValid = $this->userValidator->usernameValidate($username);
            } catch (InvalidArgumentException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                $isValid = false;
            }
        } while (!$isValid);

        return $username;
    }

    private function askValidPassword(InputInterface $input, OutputInterface $output): string
    {
        do {
            $password = $this->helper->ask($input, $output, new Question('Please enter the password: '));

            try {
                $isValid = $this->userValidator->passwordValidate($password);
            } catch (InvalidArgumentException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                $isValid = false;
            }
        } while (!$isValid);

        return $password;
    }
}
