<?php

namespace App\Repository;

use App\Entity\Pub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pub>
 *
 * @method Pub|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pub|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pub[]    findAll()
 * @method Pub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Pub::class);
        $this->em = $em;
    }

    public function save(Pub $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pub $entity, bool $flush = false): void
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
        $qb->where('ad.status = :status');
        $qb->setParameter('status', Pub::STATUS_PAYED);
        if ($startDate && $endDate) {
            $qb->andWhere('ad.created BETWEEN :startDate AND :endDate');
            $qb->setParameter('startDate', $startDate);
            $qb->setParameter('endDate', $endDate);
        }

        $result = $qb->getQuery()->getSingleResult();
        return $result['amount'] ?? 0;
    }

    public function countAdsBetween($startDate, $endDate): int
    {
        $qb = $this->createQueryBuilder('ad');
        $qb->select('COUNT(ad.id) as total');
        if ($startDate && $endDate) {
            $qb->where('ad.created BETWEEN :startDate AND :endDate');
            $qb->setParameter('startDate', $startDate);
            $qb->setParameter('endDate', $endDate);
        }
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
            ->setParameter('status', Pub::STATUS_PAYED)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Pub[] Returns an array of Pub objects
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

//    public function findOneBySomeField($value): ?Pub
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
