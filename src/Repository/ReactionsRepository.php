<?php

namespace App\Repository;

use App\Entity\Reactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reactions>
 *
 * @method Reactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reactions[]    findAll()
 * @method Reactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reactions::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Reactions $entity, bool $flush = true): void
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
    public function remove(Reactions $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPostReactions(int $postId): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT SUM(`value`) FROM `reactions` WHERE `pid` = :postId AND `type` = 0;';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('postId', $postId);
        $resultSet = $stmt->executeQuery();

        $result = $resultSet->fetchOne();
        $result = $result ? $result : 0;

        return $result;
    }

    public function getCommentReactions(int $commentId): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT SUM(`value`) FROM `reactions` WHERE `pid` = :commentId AND `type` = 1;';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('commentId', $commentId);
        $resultSet = $stmt->executeQuery();

        $result = $resultSet->fetchOne();
        $result = $result ? $result : 0;

        return $result;
    }

    // /**
    //  * @return Reactions[] Returns an array of Reactions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reactions
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
