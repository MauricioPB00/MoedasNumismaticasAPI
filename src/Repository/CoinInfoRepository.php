<?php

namespace App\Repository;

use App\Entity\CoinInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BanknBanknotInfoote>
 *
 * @method CoinInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoinInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoinInfo[]    findAll()
 * @method CoinInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoinInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoinInfo::class);
    }

    public function add(CoinInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CoinInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
