<?php

namespace App\Repository;

use App\Entity\Stagiaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stagiaire>
 *
 * @method Stagiaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stagiaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stagiaire[]    findAll()
 * @method Stagiaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaire::class);
    }


    public function findByStagiairesNotInSession(int $id)
    {
        $em = $this->getEntityManager(); // get the EntityManager
        $sub = $em->createQueryBuilder(); // create a new QueryBuilder
        $qb = $sub; // use the same QueryBuilder for the subquery
        $qb->select('s') // select the root alias
            ->from('App\Entity\Stagiaire', 's') // the subquery is based on the same entity
            ->leftJoin('s.session_stagiaire', 'se') // join the subquery
            ->where('se.id = :id');
        $sub = $em->createQueryBuilder(); // create a new QueryBuilder
        $sub->select('st')->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            ->setParameter('id', $id);
        return $sub->getQuery()->getResult();
    }

    // public function findTraineeNotSubscribed($session)
    // {
    //     $em = $this->getEntityManager();
    //     $sub = $em->createQueryBuilder();
    //     $qb = $sub;
    //     $qb =  $session->getStagiaires();
    // }


    //    /**
    //     * @return Stagiaire[] Returns an array of Stagiaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stagiaire
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
