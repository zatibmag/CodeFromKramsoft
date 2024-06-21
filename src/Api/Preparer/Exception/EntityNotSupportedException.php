<?php

namespace App\Api\Preparer\Exception;

use Exception;

class EntityNotSupportedException extends Exception
{
    public function __construct(string $message = 'Entity is not supported')
    {
        parent::__construct($message);
    }
}
