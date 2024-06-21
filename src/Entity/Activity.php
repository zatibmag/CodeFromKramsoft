<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ActivityRepository;

/** @ORM\Entity(repositoryClass=ActivityRepository::class) */
class Activity
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @var Table
     * @ORM\ManyToOne(targetEntity=Table::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Table $list;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $movedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="activity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Task $task;

    public function __construct(Table $list, \DateTimeImmutable $movedAt, Task $task)
    {
        $this->list    = $list;
        $this->movedAt = $movedAt;
        $this->task    = $task;
    }

    public function onDay(\DateTimeInterface $day): bool
    {
        return $this->movedAt->setTime(0, 0, 0)->format('d') === $day->setTime(0, 0, 0)->format('d');
    }

    public function getList(): Table
    {
        return $this->list;
    }

    public function getMovedAt(): \DateTimeImmutable
    {
        return $this->movedAt;
    }

    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'list'    => $this->list->getId(),
            'movedAt' => $this->movedAt->format('Y-m-d H:i:s'),
        ];
    }
}
