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

    public function filtrer($site, $mot, $dateDeb, $dateFin, $organisateur, $participateur, $nonparticipant, $past, $user)
    {
        $querybuild = $this->createQueryBuilder('so')
            ->leftJoin('so.site', 's')
            ->leftJoin('so.etat', 'e')
            ->where("so.nom  LIKE :nom")
            ->setParameter("nom","%".$mot."%");
        if($dateDeb!=""){
            $querybuild->andWhere("so.dateHeureDebut > :dateD")
                ->setParameter("dateD",$dateDeb);
        }
        if($dateFin!=""){
            $querybuild->andWhere("so.dateHeureDebut < :dateF")
                ->setParameter("dateF",$dateFin);
        }
        if($organisateur=="on"){
            if($participateur!="on" and $nonparticipant!="on"){ //uniquement sortie organisé
                $querybuild->andWhere("so.organisateur = :user")
                    ->setParameter("user", $user);
            } elseif ($participateur != "on" or $nonparticipant != "on") {
                if ($participateur == "on") { //sortie organisé + inscrit
                    $querybuild->andWhere("so.organisateur = :user or :pUser MEMBER OF so.participants")
                        ->setParameter("pUser", $user);
                } elseif ($nonparticipant == "on") {
                    $querybuild->andWhere("so.organisateur = :user or :pUser NOT MEMBER OF so.participants")
                        ->setParameter("pUser", $user);
                }
                $querybuild->setParameter("user", $user);
            }
            //else : les trois sont activés : toutes les sorties sauf ceux passées (géré dans le else de $past)
        } else {
            //gestion filtre participant + nonparticipant sachant que non organisé
            if ($participateur != "on" or $nonparticipant != "on") {
                if ($participateur == "on") {
                    $querybuild->andWhere(":pUser MEMBER OF so.participants")
                        ->setParameter("pUser", $user);
                }
                if ($nonparticipant == "on") {
                    $querybuild->andWhere(":nUser NOT MEMBER OF so.participants")
                        ->andWhere(":nUser != so.organisateur")
                        ->setParameter("nUser", $user);
                }
            } else {
                $querybuild->andWhere(":xUser != so.organisateur")
                    ->setParameter("xUser", $user);
            }
        }
        if($past=="on"){
            if($organisateur=="on" or $participateur=="on" or $nonparticipant=="on"){
                $querybuild->orWhere("e.libelle = 'passee' and so.dateHeureDebut < :mois")
                    ->setParameter("mois", date("d-m-Y", strtotime("-1 month")));
            } else {
                $querybuild->andWhere("e.libelle = 'passee'")
                    ->andWhere("so.dateHeureDebut < :mois")
                    ->setParameter("mois", date("d-m-Y", strtotime("-1 month")));
            }
        } else {
            if($organisateur=="on" or $participateur=="on" or $nonparticipant=="on"){
                $querybuild->andWhere("e.libelle != 'passee'");
            } else { //pas de filtre coché : pas de résultat. Requête rendant forcément 0 résultat
                $querybuild->andWhere("e.libelle != 'passee'")
                    ->andWhere("e.libelle = 'passee'");
            }
        }

        $query = $querybuild->getQuery();
        return $query->getResult();

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
