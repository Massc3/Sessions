<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findByStagiairesNotInSession(int $id)
    {
        $em = $this->getEntityManager(); //recuperation des class dans l'entity manager de la session
        $sub = $em->createQueryBuilder(); // Création/Preparation? d'une nouvelle requete situé dans notre formulaire
        $qb = $sub; // use the same QueryBuilder for the subquery / Utilisation de la meme requete pour l'execution de la demande 
        $qb->select('s') // select the root alias / definir la bonne root de la requete, recuperation des stagiaires inscrits dans la session
            ->from('App\Entity\Stagiaire', 's') // the subquery is based on the same entity / definir de la ou on part et faire le chemin de chaque entité et class par lesquels on passe
            ->leftJoin('s.sessions', 'se') // join the subquery / Relier les 2 entitées 
            ->where('se.id = :id');
        $sub = $em->createQueryBuilder(); // recuperation des stagiaires non inscrits
        $sub->select('st')->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            ->setParameter('id', $id);
        return $sub->getQuery()->getResult();
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
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

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
