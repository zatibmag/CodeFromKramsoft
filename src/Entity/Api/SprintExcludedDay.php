<?php

namespace App\Entity\Api;

use App\Repository\Api\SprintExcludedDayRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SprintExcludedDayRepository::class)
 */
class SprintExcludedDay implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Sprint::class, inversedBy="excludedDays", cascade={"persist"})
     */
    private ?Sprint $sprint = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): void
    {
        $this->sprint = $sprint;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d H:i:s')
        ];
    }
}
