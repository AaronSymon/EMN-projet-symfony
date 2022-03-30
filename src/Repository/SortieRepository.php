<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sortie $entity, bool $flush = true): void
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
    public function remove(Sortie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function filtrer($site, $mot, $dateDeb, $dateFin, $organisateur, $participateur, $nonparticipant, $past, $user): void
    {
        $querybuild = $this->createQueryBuilder('so')
            ->leftJoin('so.site', 's')
            ->leftJoin('so.etat', 'e')
            ->where("so.nom  LIKE :nom")
            ->setParameter("nom","%".$mot."%");
        if($past=="on"){
            $querybuild->andWhere("e.libelle = 'passee'")
                ->andWhere("so.dateHeureDebut < :mois")
                ->setParameter("mois",date("d-m-Y", strtotime("-1 month")));
        } else {
            if($organisateur=="on" or $participateur=="on" or $nonparticipant=="on"){
                $querybuild->andWhere("e.libelle != 'passee'");
            }
        }
        if($dateDeb!=""){
            $querybuild->andWhere("so.dateHeureDebut > :dateD")
                ->setParameter("dateD",$dateDeb);
        }
        if($dateFin!=""){
            $querybuild->andWhere("so.dateHeureDebut < :dateF")
                ->setParameter("dateF",$dateFin);
        }
        if($organisateur=="on"){
            $querybuild->andWhere("so.organisateur = :user")
                ->setParameter("user",$user);
        }

        if($participateur=="on") {
            $querybuild->andWhere(":pUser MEMBER OF so.participants")
                ->setParameter("pUser", $user);
        }
        if ($nonparticipant == "on") {
            $querybuild->andWhere(":nUser NOT MEMBER OF so.participants")
                ->setParameter("nUser", $user);
        }


    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
