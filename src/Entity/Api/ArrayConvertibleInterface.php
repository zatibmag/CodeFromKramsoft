<?php

namespace App\Entity\Api;

interface ArrayConvertibleInterface
{
    public function toArray(): array;

    public static function fromArray(array $data);
}
