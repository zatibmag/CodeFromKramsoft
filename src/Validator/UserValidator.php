<?php

namespace App\Validator;

use InvalidArgumentException;

class UserValidator extends AbstractValidator
{

    public function usernameValidate(?string $answer): bool
    {
        if ($this->isEmpty($answer)) {
            throw new InvalidArgumentException('Value is empty');
        }

        if ($this->isUserExists(['username' => $answer])) {
            throw new InvalidArgumentException('User with this username already exists');
        }

        return true;
    }

    public function passwordValidate(?string $answer): bool
    {
        if ($this->isEmpty($answer)) {
            throw new InvalidArgumentException('Value is empty');
        }

        return true;
    }
}
