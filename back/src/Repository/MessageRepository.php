<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\UserToUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);


        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithAtLeast2Reports(): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.reports', 'r')
            ->groupBy('m.id')
            ->having('COUNT(r.id) >= 2')
            ->getQuery()
            ->getResult();
    }

    public function getFeed($userId, $limit, $page = 1)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m')
            ->leftJoin('m.shares', 's')
            ->leftJoin('s.sharingBy', 'u')
            ->where('m.creator IN (:following)')
            ->orWhere('u = :userId')
            ->orWhere('m.creator = :userId')
            ->orWhere('s.sharingBy IN (:following)')
            ->setParameter('following', $this->getFollowingIds($userId))
            ->setParameter('userId', $userId)
            ->orderBy('m.created', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->execute();
    }

    private function getFollowingIds($userId)
    {
        $qb = $this->getEntityManager()->getRepository(UserToUser::class)->createQueryBuilder('u')
            ->select('IDENTITY(u.other)')
            ->where('u.me = :userId')
            ->setParameter('userId', $userId);

        return $qb->getQuery()->getScalarResult();
    }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
