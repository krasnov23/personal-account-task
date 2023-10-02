<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function sortTransactions(int $id, string $startDate = null, string $endDate = null, string $serviceName = null)
    {
        $query = $this->createQueryBuilder('t')
            ->addSelect('u')
            ->leftJoin("t.userAccount",'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        if ($startDate) {
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

        return $query->orderBy('t.date', 'DESC')->getQuery()->getResult();
    }

    //    /**
    //     * @return Transaction[] Returns an array of Transaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Transaction
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /*public function findTransactionsWithStartDate(int $userAccountId, $date): array
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :useraccountId MEMBER OF t.userAccount
            AND t.date > :date')
                ->setParameter('date',$date)
                ->setParameter('userAccountId',$userAccountId)
                ->getResult();
        }

        public function findTransactionsWithEndDate($date,int $userAccountId): array
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :useraccountId MEMBER OF t.userAccount
            AND t.date < :date')
                ->setParameter('date',$date)
                ->setParameter('userAccountId',$userAccountId)
                ->getResult();
        }

        public function findTransactionWithName(string $name): array
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :userAccountId MEMBER OF t.userAccount
            AND t.serviceName = :name')->setParameter('name',$name)->getResult();
        }

        public function findTransactionsWithStartAndEndDate(int $id,$startDate,$endDate): array
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :userAccountId MEMBER OF t.userAccount AND
            t.date BETWEEN :startDate AND :endDate')
                ->setParameter('userAccountId',$id)
                ->setParameter('startDate',$startDate)
                ->setParameter('endDate',$endDate)
                ->getResult();
        }

        public function findTransactionWithStartDateAndName(int $id,$startDate, string $name): array
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :userAccountId MEMBER OF t.userAccount AND
            t.date > :startDate AND t.serviceName = :name')
                ->setParameter('userAccountId',$id)
                ->setParameter('startDate',$startDate)
                ->setParameter('name',$name)
                ->getResult();
        }

        public function findTransactionWithEndDateAndName(int $id,$date, string $name)
        {
            return $this->_em->createQuery('SELECT t FROM App\Entity\Transaction t WHERE :userAccountId MEMBER OF t.userAccount AND
            t.date < :date AND t.serviceName = :name')
                ->setParameter('userAccountId',$id)
                ->setParameter('startDate',$date)
                ->setParameter('name',$name)
                ->getResult();
        }*/
}
