<?php

namespace App\DataFixtures;

use App\Api\Preparer\ChartLinePreparer;
use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use App\Entity\ChartLine;
use App\Entity\Table;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;

class SprintFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->prepareSprints($manager);
        $manager->flush();
    }

    private function prepareSprints(ObjectManager $manager): void
    {
        $startDate = new DateTime('-7 days');
        $endDate   = new DateTime();

        $list = $manager->getRepository(Table::class)->findOneBy([], ['id' => 'ASC']);

        $totalTasksDone = 0;

        for ($i = 1; $i <= 20; $i++) {
            $sprint = new Sprint();
            $sprint->setName('Sprint ' . $i);

            $tasksDone = $totalTasksDone + rand(10, 20);
            $sprint->setCapacity($tasksDone);

            $totalTasksDone = $tasksDone;

            $startAt = clone $startDate;
            $startAt->modify("+$i days");
            $sprint->setStartAt($startAt);

            $endAt = clone $endDate;
            $endAt->modify("+$i days");
            $sprint->setEndAt($endAt);
            $sprint->setListDoneId($list->getId());
            $sprint->setCapacityType(0);
            $sprintStories = $this->prepareSprintStories($manager, $startAt, $endAt);
            $sprint->setSprintStories($sprintStories);

            $preparer = new ChartLinePreparer();

            $currentChartLine = $preparer->prepareChartLine($sprint, new ChartLine());
            $perfectChartLine = $preparer->preparePerfectChartLine($sprint, new ChartLine());

            $sprint->setPerfectChartLine($perfectChartLine);
            $sprint->setCurrentChartLine($currentChartLine);

            $manager->persist($currentChartLine);
            $manager->persist($perfectChartLine);

            $manager->persist($sprint);
            $manager->flush();
        }
    }

    private function prepareSprintStories(ObjectManager $manager, DateTime $startAt, DateTime $endAt): ArrayCollection
    {
        $sprintStories = [];

        $currentDate = clone $startAt;
        $tasksDone   = 0;

        while ($currentDate <= $endAt) {
            $tasksDone += rand(0, 5);

            $story = new SprintStory();
            $story->setCapacity($tasksDone);

            $reflectionClass   = new ReflectionClass($story);
            $createdAtProperty = $reflectionClass->getProperty('createdAt');
            $createdAtProperty->setAccessible(true);
            $createdAtValue = clone $currentDate;
            $createdAtProperty->setValue($story, $createdAtValue);

            $manager->persist($story);
            $sprintStories[] = $story;

            $currentDate->modify('+1 day');
        }

        return new ArrayCollection($sprintStories);
    }
}
