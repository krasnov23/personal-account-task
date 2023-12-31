<?php

namespace App\Repository;

use App\Entity\UserAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAccount>
 *
 * @method UserAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAccount[]    findAll()
 * @method UserAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccount::class);
    }

    public function save(UserAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllUserServices(int $id): UserAccount
    {
        return $this->createQueryBuilder('u')
            ->addSelect('s')
            ->leftJoin('u.userServices','s')
            ->where('u.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserTransactions(int $id, ?string $startDate = null, ?string $endDate = null, ?string $serviceName = null): ?UserAccount
    {
        $query = $this->createQueryBuilder('u')
            ->addSelect('t')
            ->leftJoin('u.userTransactions','t')
            ->where('u.id = :id')
            ->setParameter('id',$id)
        ;

        if ($startDate)
        {
            $query->andWhere('t.date >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $query->andWhere('t.date <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        if ($serviceName) {
            $query->andWhere('t.serviceName = :name')
                ->setParameter('name', $serviceName);
        }

        return $query->orderBy('t.date', 'DESC')->getQuery()->getOneOrNullResult();

    }




    //    /**
    //     * @return UserAccount[] Returns an array of UserAccount objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserAccount
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
