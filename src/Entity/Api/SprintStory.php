<?php

namespace App\Entity\Api;

use App\Repository\Api\SprintStoryRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SprintStoryRepository::class)
 */
class SprintStory implements EntityInterface
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
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacity;

    /**
     * @ORM\ManyToOne(targetEntity=Sprint::class, inversedBy="sprintStories")
     */
    private ?Sprint $sprint = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $value): void
    {
        $this->capacity = $value;
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
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'capacity'  => $this->capacity,
        ];
    }
}
