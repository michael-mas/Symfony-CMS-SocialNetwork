<?php

namespace App\Repository;

use App\Entity\Pages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pages>
 *
 * @method Pages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pages[]    findAll()
 * @method Pages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pages::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Page $entity, bool $flush = true): void
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
    public function remove(Page $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPages(array $uids, int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $idList = implode(' OR `author` = ',$uids);

        $sql = 'SELECT * FROM `pages` WHERE (`status` = 0 OR `status` = 1) AND (`author` = '.$idList.') ORDER BY `date` DESC LIMIT '.$limit.' OFFSET '.$offset.';';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function getUserPages(int $uid, int $limit = 10, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `pages` WHERE `author` = '.$uid.' AND (`status` = 0 OR `status` = 1) ORDER BY `date` DESC LIMIT '.$limit.' OFFSET '.$offset.';';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function searchPages(string $search, int $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `pages` WHERE (`status` = 0 OR `status` = 1) AND (`title` LIKE :search OR `content` LIKE :search) ORDER BY `date` DESC LIMIT 10 OFFSET '.$offset.';';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('search', '%'.$search.'%');
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    // /**
    //  * @return Pages[] Returns an array of Pages objects
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
    public function findOneBySomeField($value): ?Pages
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
