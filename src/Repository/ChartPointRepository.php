<?php

namespace App\Repository;

use App\Entity\ChartPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChartPoint>
 *
 * @method ChartPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChartPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChartPoint[]    findAll()
 * @method ChartPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartPoint::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ChartPoint $entity, bool $flush = true): void
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
    public function remove(ChartPoint $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
