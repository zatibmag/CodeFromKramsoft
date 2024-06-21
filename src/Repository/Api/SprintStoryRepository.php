<?php

namespace App\Repository\Api;

use App\Entity\Api\Sprint;
use App\Entity\Api\SprintStory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SprintStory>
 *
 * @method SprintStory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SprintStory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SprintStory[]    findAll()
 * @method SprintStory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintStoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SprintStory::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SprintStory $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(SprintStory $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findTasksToDoBySprint(Sprint $sprint): array
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.sprint = :sprint')
            ->setParameter('sprint', $sprint)
            ->getQuery()
            ->getResult();
    }
}
