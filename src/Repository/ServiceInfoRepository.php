<?php

namespace App\Repository;

use App\Entity\ServiceInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceInfo>
 *
 * @method ServiceInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceInfo[]    findAll()
 * @method ServiceInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceInfo::class);
    }

//    /**
//     * @return ServiceInfo[] Returns an array of ServiceInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ServiceInfo
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
