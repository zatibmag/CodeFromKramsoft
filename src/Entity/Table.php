<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TableRepository;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="list")
 */
class Table
{
    /** @ORM\Column(type="string") */
    private string $title;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="list", cascade={"persist"})
     */
    private Collection $tasks;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $position;

    public function __construct(string $title, int $order)
    {
        $this->title    = $title;
        $this->tasks    = new ArrayCollection();
        $this->position = $order;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function move(int $order): void
    {
        $this->position = $order;
    }

    public function addTask(Task $task): void
    {
        $this->tasks->add($task);
    }

    public function removeTask(Task $task): void
    {
        $this->tasks->removeElement($task);
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'tasks' => $this->tasks->map(fn(Task $task) => $task->toArray()),
        ];
    }
}
