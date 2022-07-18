<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Posts $entity, bool $flush = true): void
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
    public function remove(Posts $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPosts(array $uids, int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $idList = implode(' OR `author` = ',$uids);

        $sql = 'SELECT * FROM `posts` WHERE (`status` = 0 OR `status` = 1) AND (`author` = '.$idList.') ORDER BY `date` DESC LIMIT '.$limit.' OFFSET '.$offset.';';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function getUserPosts(int $uid, int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `posts` WHERE `author` = '.$uid.' AND (`status` = 0 OR `status` = 1) ORDER BY `date` DESC LIMIT '.$limit.' OFFSET '.$offset.';';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function searchPosts(string $search, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `posts` WHERE (`status` = 0 OR `status` = 1) AND (`title` LIKE :search OR `content` LIKE :search) ORDER BY `date` DESC LIMIT 10 OFFSET '.$offset.';';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('search', '%'.$search.'%');
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return Posts[] Returns an array of Posts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Posts
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
