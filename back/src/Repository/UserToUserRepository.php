<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\UserToUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserToUser>
 *
 * @method UserToUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserToUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserToUser[]    findAll()
 * @method UserToUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserToUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToUser::class);
    }

    public function save(UserToUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserToUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFollows($userId, $limit, $page)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.me = :userId')
            ->setParameter('userId', $userId)
            ->innerJoin('u.other', 'o')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $paginator = new Paginator($qb);
        return $paginator;
    }

    public function getFollowers($userId, $limit, $page)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.other = :userId')
            ->setParameter('userId', $userId)
            ->innerJoin('u.me', 'm')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $paginator = new Paginator($qb);
        return $paginator;
    }

//    /**
//     * @return UserToUser[] Returns an array of UserToUser objects
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

//    public function findOneBySomeField($value): ?UserToUser
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
