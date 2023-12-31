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

    public function save(ServiceInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ServiceInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findServiceByNameAndPriceNotNull(string $name): ServiceInfo
    {
        return $this->_em->createQuery('SELECT s FROM App\Entity\ServiceInfo s WHERE s.name = :name AND s.amount IS NULL')
            ->setParameter('name', $name)->getSingleResult();
    }

    public function findServicesNamesByUser(int $id): array
    {
        return $this->_em->createQuery('SELECT s FROM App\Entity\ServiceInfo s WHERE :useraccountId MEMBER OF s.userAccounts')
            ->setParameter('useraccountId', $id)->getResult();
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
