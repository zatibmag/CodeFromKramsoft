<?php

namespace App\Api\Preparer;

use App\Entity\Api\SprintExcludedDay;
use App\Entity\ChartLine;
use App\Entity\ChartPoint;
use App\Entity\Api\Sprint;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ChartLinePreparer
{
    public function prepareChartLine(Sprint $sprint, ChartLine $chartLine): ChartLine
    {
        $sprintStories = $sprint->getSprintStories();

        $latestStories = [];

        foreach ($sprintStories as $sprintStory) {
            $date = $sprintStory->getCreatedAt()->format('Y-m-d');

            $endAt = clone $sprint->getEndAt();
            $endAt->modify("23:59:59");

            if ($sprintStory->getCreatedAt() > $endAt) {
                continue;
            }

            if ($sprintStory->getCreatedAt() < $sprint->getStartAt()) {
                continue;
            }

            if (!isset($latestStories[$date]) || $sprintStory->getCreatedAt() > $latestStories[$date]->getCreatedAt()) {
                $latestStories[$date] = $sprintStory;
            }
        }

        foreach ($chartLine->getChartPoints() as $chartPoint) {
            $date = $chartPoint->getDate()->format('Y-m-d');
            if (isset($latestStories[$date])) {
                $chartLine->removeChartPoint($chartPoint);
            }
        }

        foreach ($latestStories as $sprintStory) {
            $chartPoint = new ChartPoint();
            $chartPoint->setDate($sprintStory->getCreatedAt());
            $chartPoint->setValue($sprint->getCapacity() - $sprintStory->getCapacity());
            $chartLine->addChartPoint($chartPoint);
        }

        $chartLine->sortChartLine();

        return $chartLine;
    }

    public function preparePerfectChartLine(Sprint $sprint, ChartLine $chartLine): ChartLine
    {
        $startAt = $sprint->getStartAt();
        $endAt   = $sprint->getEndAt();
        $value   = $chartLine->getCapacity() ?? $sprint->getCapacity();

        return $this->generateChartLine($startAt, $endAt, $value, $sprint, $chartLine);
    }

    public function prepareCapacity(
        DateTimeInterface $startAt,
        DateTimeInterface $endAt,
        float $value,
        Sprint $sprint
    ): ChartLine {
        return $this->generateChartLine($startAt, $endAt, $value, $sprint, new ChartLine());
    }

    public function prepareCustomChartLine(Sprint $sprint, ChartLine $chartLine): ChartLine
    {
        $newChartPoints = new ArrayCollection();
        $capacityDays   = $chartLine->getCapacityDayChartPoints()->toArray();
        usort($capacityDays, function ($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        $lines = $this->generateCapacityLines($sprint, $chartLine, $capacityDays);

        $index = 0;
        foreach ($lines as $line) {
            foreach ($line->getChartPoints()->toArray() as $point) {
                $newChartPoints->add($point);
                if (isset($capacityDays[$index])
                    && $point->getDate()->format('Y-m-d') === $capacityDays[$index]->getDate()->format('Y-m-d')) {
                    break;
                }
            }
            $index++;
        }
        $chartLine->setChartPoints($newChartPoints);

        return $chartLine;
    }

    private function generateChartLine(
        DateTimeInterface $startAt,
        DateTimeInterface $endAt,
        float $value,
        Sprint $sprint,
        ChartLine $chartLine
    ): ChartLine {
        $interval             = $startAt->diff($endAt);
        $days                 = $interval->days;
        $filteredExcludedDays = $this->getExcludedDaysInRange($startAt, $endAt, $sprint);
        $perfectPerDay        = $value / ($days - count($filteredExcludedDays));
        $date                 = clone $startAt;

        $chartLine->removePointsBeforeDate($startAt);
        $chartLine->removePointsAfterDate($endAt);

        $this->addOrUpdateChartPoint($chartLine, $date, $value);

        for ($i = 1; $i <= $days; $i++) {
            if (!$sprint->isDayExcluded($date)) {
                $value -= $perfectPerDay;
            }
            $date->modify('+1 days');
            $this->addOrUpdateChartPoint($chartLine, $date, $value);
        }

        return $chartLine;
    }

    private function addOrUpdateChartPoint(ChartLine $chartLine, DateTimeInterface $date, float $value): void
    {
        $chartPoint = $chartLine->hasPointForDate($date);
        if (!$chartPoint) {
            $chartPoint = new ChartPoint();
            $chartPoint->setDate(clone $date);
            $chartLine->addChartPoint($chartPoint);
        }
        $chartPoint->setValue($value);
    }

    private function getExcludedDaysInRange(DateTimeInterface $startAt, DateTimeInterface $endAt, Sprint $sprint): array
    {
        $excludedDays = $sprint->getExcludedDays()->toArray() ?? [];

        return array_filter($excludedDays, function (SprintExcludedDay $day) use ($startAt, $endAt) {
            return $day->getDate() >= $startAt && $day->getDate() <= $endAt;
        });
    }

    private function generateCapacityLines(Sprint $sprint, ChartLine $chartLine, array $capacityDays): ArrayCollection
    {
        $capacityLines = new ArrayCollection();

        $capacityLines->add(
            $this->prepareCapacity($sprint->getStartAt(), $sprint->getEndAt(), $chartLine->getCapacity(), $sprint)
        );

        foreach ($capacityDays as $capacityDay) {
            $newLine = $this->prepareCapacity(
                $capacityDay->getDate(),
                $sprint->getEndAt(),
                $capacityDay->getValue(),
                $sprint
            );
            $capacityLines->add($newLine);
        }

        return $capacityLines;
    }
}
