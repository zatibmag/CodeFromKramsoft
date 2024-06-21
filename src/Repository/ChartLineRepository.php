<?php

namespace App\Repository;

use App\Entity\ChartLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChartLine>
 *
 * @method ChartLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChartLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChartLine[]    findAll()
 * @method ChartLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChartLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartLine::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ChartLine $entity, bool $flush = true): void
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
    public function remove(ChartLine $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
