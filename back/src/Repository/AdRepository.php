<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ad>
 *
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Ad::class);
        $this->em = $em;
    }

    public function save(Ad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAmountEarned($startDate, $endDate): int
    {
        $qb = $this->createQueryBuilder('ad');
        $qb->select('SUM(ad.price) as amount');
        $qb->where('ad.created BETWEEN :startDate AND :endDate');
        $qb->andWhere('ad.status = :status');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $qb->setParameter('status', AD::STATUS_PAYED);
        $result = $qb->getQuery()->getSingleResult();
        return $result['amount'] ?? 0;
    }

    public function countAdsBetween($startDate, $endDate): int
    {
        $qb = $this->createQueryBuilder('ad');
        $qb->select('COUNT(ad.id) as total');
        $qb->where('ad.created BETWEEN :startDate AND :endDate');
        $qb->setParameter('startDate', $startDate);
        $qb->setParameter('endDate', $endDate);
        $result = $qb->getQuery()->getSingleResult();
        return $result['total'] ?? 0;
    }

    public function getAdsFromToday()
    {
        return $this->createQueryBuilder('ad')
            ->select('ad')
            ->where('ad.status = :status')
            ->andWhere('ad.endDate > :now')
            ->andWhere('ad.startDate < :now')
            ->setParameter('status', AD::STATUS_PAYED)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Ad[] Returns an array of Ad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ad
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
