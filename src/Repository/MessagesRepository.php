<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Messages $entity, bool $flush = true): void
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
    public function remove(Messages $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getLastMessageDate(int $uid, int $fid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `date` FROM `messages` WHERE (`sender` = '.$uid.' AND `recipient` = '.$fid.') OR (`sender` = '.$fid.' AND `recipient` = '.$uid.') ORDER BY `date` DESC LIMIT 1;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchOne();
    }

    public function getMessages(int $uid, int $fid)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `messages` WHERE (`sender` = '.$uid.' AND `recipient` = '.$fid.') OR (`sender` = '.$fid.' AND `recipient` = '.$uid.') ORDER BY `date` ASC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function getMessagesSince(int $uid, int $fid, $date)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM `messages` WHERE ((`sender` = '.$uid.' AND `recipient` = '.$fid.') OR (`sender` = '.$fid.' AND `recipient` = '.$uid.')) AND `date` > "'.$date.'" ORDER BY `date` ASC;';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function sendMessage(int $uid, int $fid, string $message)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'INSERT INTO `messages` (`sender`, `recipient`, `content`, `date`) VALUES ('.$uid.', '.$fid.', "'.$message.'", NOW());';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet;
    }

    // /**
    //  * @return Messages[] Returns an array of Messages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
