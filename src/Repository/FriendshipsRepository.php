<?php

namespace App\Repository;

use App\Entity\Friendships;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friendships>
 *
 * @method Friendships|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendships|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendships[]    findAll()
 * @method Friendships[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendships::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Friendships $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Friendships $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getFriendList(int $uid)
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = 'SELECT `user1` as `uid`, `status`, `u1last` as `checked_me`, `u2last` as `last_checked` FROM `friendships` WHERE (`user1` = :uid OR `user2` = :uid) AND (`status` = 2 OR `status` = 4) UNION ALL SELECT `user2`, `status`, `u2last`, `u1last` FROM `friendships` WHERE (`user1` = :uid OR `user2` = :uid) AND (`status` = 2 OR `status` = 4);';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uid' => $uid]);

        return $resultSet->fetchAllAssociative();
    }

    public function getBlockedList(int $uid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `user2` as `uid`, `status` FROM `friendships` WHERE `user1` = :uid AND (`status` = 3 OR `status` = 4);';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uid' => $uid]);

        return $resultSet->fetchAllAssociative();
    }

    public function getPendingList(int $uid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `user2` as `uid` FROM `friendships` WHERE `user1` = :uid AND `status` = 1 ORDER BY `u1last` DESC;';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uid' => $uid]);

        return $resultSet->fetchAllAssociative();
    }

    public function getRequestList(int $uid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `user1` as `uid` FROM `friendships` WHERE `user2` = :uid AND `status` = 1 ORDER BY `u2last` DESC;';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uid' => $uid]);

        return $resultSet->fetchAllAssociative();
    }
    
    // /**
    //  * @return Friendships[] Returns an array of Friendships objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Friendships
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
