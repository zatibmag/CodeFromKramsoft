<?php

namespace App\Repository\Api;

use App\Entity\Api\SprintExcludedDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SprintExcludedDay>
 *
 * @method SprintExcludedDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method SprintExcludedDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method SprintExcludedDay[]    findAll()
 * @method SprintExcludedDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintExcludedDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SprintExcludedDay::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SprintExcludedDay $entity, bool $flush = true): void
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
    public function remove(SprintExcludedDay $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
