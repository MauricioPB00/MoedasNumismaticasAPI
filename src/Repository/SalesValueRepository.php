<?php

namespace App\Repository;

use App\Entity\SalesValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesValue>
 *
 * @method SalesValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalesValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalesValue[]    findAll()
 * @method SalesValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalesValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesValue::class);
    }

    public function add(SalesValue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SalesValue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
