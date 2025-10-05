<?php

namespace App\Repository;

use App\Entity\BanknoteInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BanknBanknotInfoote>
 *
 * @method BanknoteInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method BanknoteInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method BanknoteInfo[]    findAll()
 * @method BanknoteInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanknoteInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BanknoteInfo::class);
    }

    public function add(BanknoteInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BanknoteInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
