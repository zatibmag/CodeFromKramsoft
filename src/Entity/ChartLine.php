<?php

namespace App\Entity;

use App\Entity\Api\ArrayConvertibleInterface;
use App\Entity\Api\EntityInterface;
use App\Entity\Api\Sprint;
use App\Repository\ChartLineRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChartLineRepository::class)
 */
class ChartLine implements ArrayConvertibleInterface, EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=ChartPoint::class, mappedBy="chartLine", cascade={"persist", "remove"})
     */
    private Collection $chartPoints;

    /**
     * @ORM\OneToMany(targetEntity=CapacityDayChartPoint::class, mappedBy="chartLine", cascade={"persist", "remove"})
     */
    private Collection $capacityDayChartPoints;

    /**
     * @ORM\ManyToOne(targetEntity=Sprint::class, inversedBy="chartLines")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sprint;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capacity;

    public function __construct()
    {
        $this->chartPoints            = new ArrayCollection();
        $this->capacityDayChartPoints = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ChartPoint>
     */
    public function getChartPoints(): Collection
    {
        return $this->chartPoints;
    }

    public function setChartPoints(ArrayCollection $chartPoints): void
    {
        $this->chartPoints = $chartPoints;
    }

    public function removeChartPoints(ArrayCollection $chartPoints): void
    {
        foreach ($chartPoints as $chartPoint) {
            $this->removeChartPoint($chartPoint);
        }
    }

    public function addChartPoint(ChartPoint $chartPoint): self
    {
        if (!$this->chartPoints->contains($chartPoint)) {
            $this->chartPoints[] = $chartPoint;
            $chartPoint->setChartLine($this);
        }

        return $this;
    }

    public function removeChartPoint(ChartPoint $chartPoint): self
    {
        if ($this->chartPoints->removeElement($chartPoint)) {
            if ($chartPoint->getChartLine() === $this) {
                $chartPoint->setChartLine(null);
            }
        }

        return $this;
    }

    public function getCapacityDayChartPoints(): Collection
    {
        return $this->capacityDayChartPoints;
    }

    public function setCapacityDayChartPoints(Collection $capacityDayChartPoints): void
    {
        $this->capacityDayChartPoints = $capacityDayChartPoints;
    }

    public function removeCapacityDayChartPoints(ArrayCollection $chartPoints): void
    {
        foreach ($chartPoints as $chartPoint) {
            $this->removeChartPoint($chartPoint);
        }
    }

    public function addCapacityDayChartPoint(CapacityDayChartPoint $chartPoint): self
    {
        if (!$this->chartPoints->contains($chartPoint)) {
            $this->chartPoints[] = $chartPoint;
            $chartPoint->setChartLine($this);
        }

        return $this;
    }

    public function hasPointForDate(DateTimeInterface $date): ?ChartPoint
    {
        $filteredChartPoints = $this->chartPoints->filter(
            fn(ChartPoint $chartPoint) => $chartPoint->getDate()->format('Y-m-d') === $date->format('Y-m-d')
        );

        if ($filteredChartPoints->isEmpty()) {
            return null;
        }

        return $filteredChartPoints->first();
    }

    public function isDayUpdated(DateTimeInterface $date): ?CapacityDayChartPoint
    {
        $filteredChartPoints = $this->capacityDayChartPoints->filter(
            fn(CapacityDayChartPoint $chartPoint) => $chartPoint->getDate()->format('Y-m-d') === $date->format('Y-m-d')
        );

        if ($filteredChartPoints->isEmpty()) {
            return null;
        }

        return $filteredChartPoints->first();
    }

    public function toArray(): array
    {
        $chartPoints      = $this->getChartPoints();
        $chartPointArrays = array_map(
            fn(ChartPoint $chartPoint) => $chartPoint->toArray(),
            $chartPoints->toArray()
        );

        return [
            'id'          => $this->getId(),
            'chartPoints' => array_merge($chartPointArrays),
        ];
    }

    public function getSortedArrayByDate(): array
    {
        $this->sortChartLine();

        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        $chartLine = new self();

        foreach ($data as $pointData) {
            $chartLine->addChartPoint(ChartPoint::fromArray($pointData));
        }

        return $chartLine;
    }

    public function removePointsAfterDate(DateTimeInterface $date): void
    {
        $chartPoints = $this->chartPoints->filter(
            fn(ChartPoint $chartPoint) => $chartPoint->getDate() > $date
        );

        foreach ($chartPoints as $chartPoint) {
            $this->removeChartPoint($chartPoint);
        }
    }

    public function removePointsBeforeDate(DateTimeInterface $date): void
    {
        $chartPoints = $this->chartPoints->filter(
            fn(ChartPoint $chartPoint) => $chartPoint->getDate() < $date
        );

        foreach ($chartPoints as $chartPoint) {
            $this->removeChartPoint($chartPoint);
        }
    }

    public function sortChartLine(): void
    {
        $chartPointArray = $this->chartPoints->toArray();

        uasort($chartPointArray, function (ChartPoint $a, ChartPoint $b) {
            return $a->getDate() <=> $b->getDate();
        });

        $this->chartPoints = new ArrayCollection($chartPointArray);
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }
}
