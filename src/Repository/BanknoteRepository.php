<?php

namespace App\Repository;

use App\Entity\Banknote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Banknote>
 *
 * @method Banknote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banknote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banknote[]    findAll()
 * @method Banknote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanknoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banknote::class);
    }

    public function add(Banknote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Banknote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
