<?php

namespace App\Repository;

use App\Entity\CapacityDayChartPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CapacityDayChartPoint>
 *
 * @method CapacityDayChartPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method CapacityDayChartPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method CapacityDayChartPoint[]    findAll()
 * @method CapacityDayChartPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CapacityDayChartPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapacityDayChartPoint::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CapacityDayChartPoint $entity, bool $flush = true): void
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
    public function remove(CapacityDayChartPoint $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
