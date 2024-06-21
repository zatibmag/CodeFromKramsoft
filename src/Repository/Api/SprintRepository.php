<?php

namespace App\Repository\Api;

use App\Entity\Api\Sprint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sprint>
 *
 * @method Sprint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sprint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sprint[]    findAll()
 * @method Sprint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sprint::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sprint $entity, bool $flush = true): void
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
    public function remove(Sprint $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Sprint|null
     * @throws NonUniqueResultException
     */
    public function getCurrentSprint(): ?Sprint
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.startAt <= :currentDate')
            ->andWhere('s.endAt >= :currentDate')
            ->setParameter('currentDate', (new \DateTime())->format('Y-m-d'))
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
