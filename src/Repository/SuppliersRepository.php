<?php

namespace App\Repository;

use App\Entity\Suppliers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Suppliers>
 *
 * @method Suppliers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suppliers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suppliers[]    findAll()
 * @method Suppliers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuppliersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suppliers::class);
    }

    public function add(Suppliers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Suppliers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByNameAndCity(string $name, string $city): ?Suppliers
    {
        return $this->createQueryBuilder('s')
            ->where('s.name = :name')
            ->andWhere('s.city = :city')
            ->setParameter('name', $name)
            ->setParameter('city', $city)
            ->getQuery()
            ->getOneOrNullResult();  
    }
}
