<?php

namespace App\DataFixtures;

use App\Entity\Table;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->prepareLists($manager);
        $manager->flush();
    }

    private function prepareLists(ObjectManager $manager): void
    {
        $lists = [
            'To Do',
            'In Progress',
            'Done',
        ];

        foreach ($lists as $index => $list) {
            $table = new Table($list, $index + 1);
            $this->addReference($list, $table);
            $this->prepareTasks($manager, $table, $index + 2);
            $manager->persist($table);
        }
    }

    private function prepareTasks(ObjectManager $manager, Table $table, int $noOfTasks): void
    {
        for ($i = 0; $i < $noOfTasks; $i++) {
            $task = new Task('Task ' . $i, $table, 1);
            $manager->persist($task);
        }
    }
}
