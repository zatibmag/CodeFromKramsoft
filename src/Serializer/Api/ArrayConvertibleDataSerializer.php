<?php

namespace App\Serializer\Api;

use App\Entity\Api\ArrayConvertibleInterface;
use Exception;
use Symfony\Component\Serializer\SerializerInterface;

class ArrayConvertibleDataSerializer implements SerializerInterface
{
    public function serialize($data, string $format = 'json', array $context = []): string
    {
        if (!$this->support($data)) {
            throw new Exception('Unsupported data format');
        }

        return json_encode($data->toArray());
    }

    /**
     * @throws Exception
     */
    public function deserialize($data, string $type, string $format, array $context = [])
    {
        if (!$this->validate($data)) {
            throw new Exception('Invalid data format');
        }

        return $type::fromArray($data);
    }

    private function support($data): bool
    {
        return $data instanceof ArrayConvertibleInterface;
    }

    private function validate($data): bool
    {
        return is_array($data);
    }
}
