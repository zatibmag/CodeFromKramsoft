<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;

/** @ORM\Entity(repositoryClass=TaskRepository::class) */
class Task
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $title;

    /**
     * @var Collection<Activity>
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="task", cascade={"persist"})
     */
    private Collection $activity;

    /**
     * @var Table
     * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Table $list;

    /**
     * @ORM\Column(type="integer")
     */
    private $storyPoints;

    public function __construct(string $title, Table $table, int $storyPoints)
    {
        $this->activity = new ArrayCollection();
        $this->title    = $title;
        $this->list     = $table;
        $this->registerActivity(new Activity($table, new \DateTimeImmutable(), $this));
        $this->storyPoints = $storyPoints;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function finalListAt(\DateTimeInterface $day): ?Table
    {
        $activities = \array_filter($this->activity->toArray(), fn(Activity $activity) => $activity->onDay($day));
        \usort($activities, fn(Activity $a, Activity $b) => $a->getMovedAt() >= $b->getMovedAt());

        if (!$activities) {
            return null;
        }

        return $activities[0]->getList();
    }

    public function registerActivity(Activity $activity): void
    {
        $this->activity->add($activity);
    }

    public function getCurrentList(): ?Table
    {
        return $this->list;
    }

    public function move(Table $list): void
    {
        $this->list->removeTask($this);
        $this->registerActivity(new Activity($list, new \DateTimeImmutable(), $this));
        $this->list = $list;
        $list->addTask($this);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'currentList' => $this->getCurrentList()->getId(),
            'id'          => $this->id,
            'title'       => $this->title,
            'activity'    => $this->activity->map(fn(Activity $activity) => $activity->toArray()),
            'storyPoints' => $this->storyPoints,
        ];
    }

    public function getStoryPoints(): ?int
    {
        return $this->storyPoints;
    }

    public function setStoryPoints(int $storyPoints): void
    {
        $this->storyPoints = $storyPoints;
    }
}
