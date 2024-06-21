<?php

namespace App\Entity;

use App\Entity\Api\ArrayConvertibleInterface;
use App\Entity\Api\EntityInterface;

abstract class AbstractChartPoint implements ArrayConvertibleInterface, EntityInterface
{
    protected ?int $id;

    protected ?\DateTimeInterface $date;

    protected ?int $value;

    protected ?ChartLine $chartLine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getChartLine(): ?ChartLine
    {
        return $this->chartLine;
    }

    public function setChartLine(?ChartLine $chartLine): self
    {
        $this->chartLine = $chartLine;

        return $this;
    }

    public function toArray(): array
    {
        return [
            "x" => $this->date->format('Y-m-d'),
            "y" => $this->value
        ];
    }

    abstract public static function fromArray(array $data): self;
}
