<?php

namespace App\Validator;

use App\Repository\Api\UserRepository;

abstract class AbstractValidator
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isEmpty(?string $answer): bool
    {
        return ($answer === null || trim($answer) === '');
    }

    protected function isUserExists(array $criteria): bool
    {
        return (bool)$this->userRepository->findOneBy($criteria);
    }
}
