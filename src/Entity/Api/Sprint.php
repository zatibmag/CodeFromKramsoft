<?php

namespace App\Entity\Api;

use App\Entity\ChartLine;
use App\Repository\Api\SprintRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SprintRepository::class)
 */
class Sprint implements EntityInterface, ArrayConvertibleInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero()
     * @Assert\NotBlank()
     */
    private $capacity;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     */
    private $startAt;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(propertyPath="startAt", message="End date must be greater than start date")
     */
    private $endAt;

    /**
     * @ORM\OneToMany(targetEntity=SprintStory::class, mappedBy="sprint", cascade={"persist", "remove"})
     */
    private Collection $sprintStories;

    /**
     * @ORM\Column(type="string", nullable="true")
     */
    private $listDoneId;

    /**
     * @ORM\Column(type="integer", nullable="true")
     */
    private $boardId;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacityType;

    /**
     * @ORM\OneToMany(targetEntity=SprintExcludedDay::class, mappedBy="sprint", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $excludedDays;

    /**
     * @ORM\OneToOne(targetEntity=ChartLine::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $perfectChartLine;

    /**
     * @ORM\OneToOne(targetEntity=ChartLine::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $currentChartLine;

    /**
     * @ORM\OneToMany(targetEntity=ChartLine::class, mappedBy="sprint", cascade={"remove"})
     */
    private $chartLines;

    public function __construct()
    {
        $this->sprintStories = new ArrayCollection();
        $this->excludedDays  = new ArrayCollection();
        $this->chartLines    = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getStartAt(): ?DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeInterface $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(DateTimeInterface $endAt): void
    {
        $this->endAt = $endAt;
    }

    /**
     * @return Collection<int, SprintStory>
     */
    public function getSprintStories(): Collection
    {
        return $this->sprintStories;
    }

    public function addSprintStory(SprintStory $sprintStory): void
    {
        if (!$this->sprintStories->contains($sprintStory)) {
            $this->sprintStories[] = $sprintStory;
        }
    }

    public function removeSprintStory(SprintStory $sprintStory): void
    {
        $this->sprintStories->removeElement($sprintStory);
    }

    public function setSprintStories(ArrayCollection $param): void
    {
        foreach ($param as $sprintStory) {
            $sprintStory->setSprint($this);
            $this->addSprintStory($sprintStory);
        }
    }

    public static function fromArray(array $data): Sprint
    {
        $sprint = new self();

        $sprint->setName($data['name']);
        $sprint->setCapacity($data['capacity']);
        $sprint->setStartAt($data['startAt']);
        $sprint->setEndAt($data['endAt']);
        $sprint->setSprintStories($data['sprintStories']);
        $sprint->setCapacityType($data['capacityType']);
        $sprint->setExcludedDays($data['excludedDays']);

        return $sprint;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'capacity'      => $this->capacity,
            'startAt'       => $this->startAt->format('Y-m-d'),
            'endAt'         => $this->endAt->format('Y-m-d'),
            'sprintStories' => $this->sprintStories->map(function (SprintStory $sprintStory) {
                return $sprintStory->toArray();
            })->toArray(),
            'listDoneId'    => $this->getListDoneId(),
            'capacityType'  => $this->capacityType,
            'excludedDays'  => $this->excludedDays->map(function (SprintExcludedDay $excludedDay) {
                return $excludedDay->toArray();
            })->toArray(),
        ];
    }

    public function getListDoneId(): ?string
    {
        return $this->listDoneId;
    }

    public function setListDoneId(?string $listDoneId): void
    {
        $this->listDoneId = $listDoneId;
    }

    public function getBoardId(): ?int
    {
        return $this->boardId;
    }

    public function setBoardId(int $boardId): void
    {
        $this->boardId = $boardId;
    }

    public function getCapacityType(): ?int
    {
        return $this->capacityType;
    }

    public function setCapacityType(int $capacityType): void
    {
        $this->capacityType = $capacityType;
    }

    /**
     * @return Collection<int, SprintExcludedDay>
     */
    public function getExcludedDays(): Collection
    {
        return $this->excludedDays;
    }

    public function addExcludedDay(SprintExcludedDay $excludedDay): void
    {
        if (!$this->excludedDays->contains($excludedDay)) {
            $this->excludedDays[] = $excludedDay;
            $excludedDay->setSprint($this);
        }
    }

    public function removeExcludedDay(SprintExcludedDay $excludedDay): void
    {
        if ($this->excludedDays->removeElement($excludedDay)) {
            if ($excludedDay->getSprint() === $this) {
                $excludedDay->setSprint(null);
            }
        }
    }

    public function setExcludedDays(ArrayCollection $excludedDays): void
    {
        foreach ($excludedDays as $excludedDay) {
            $this->addExcludedDay($excludedDay);
        }
    }

    public function isDayExcluded(DateTimeInterface $date): bool
    {
        foreach ($this->excludedDays as $excludedDay) {
            if ($excludedDay->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    public function getPerfectChartLine(): ?ChartLine
    {
        return $this->perfectChartLine;
    }

    public function setPerfectChartLine(ChartLine $perfectChartLine): self
    {
        $this->perfectChartLine = $perfectChartLine;

        return $this;
    }

    public function getCurrentChartLine(): ?ChartLine
    {
        return $this->currentChartLine;
    }

    public function setCurrentChartLine(?ChartLine $currentChartLine): self
    {
        $this->currentChartLine = $currentChartLine;

        return $this;
    }

    public function setChartLines(Collection $chartLines): void
    {
        $this->chartLines = $chartLines;
    }

    /**
     * @return Collection<int, ChartLine>
     */
    public function getChartLines(): Collection
    {
        return $this->chartLines;
    }

    public function addChartLine(ChartLine $chartLine): self
    {
        if (!$this->chartLines->contains($chartLine)) {
            $this->chartLines[] = $chartLine;
            $chartLine->setSprint($this);
        }

        return $this;
    }

    public function removeChartLine(ChartLine $chartLine): self
    {
        if ($this->chartLines->removeElement($chartLine)) {
            // set the owning side to null (unless already changed)
            if ($chartLine->getSprint() === $this) {
                $chartLine->setSprint(null);
            }
        }

        return $this;
    }
}
