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
            ->setParameter("nom","%".$mot."%")
            ->andWhere("s.nom = :site")
            ->setParameter("site",$site)
            ->andWhere("so.dateHeureDebut > :dateMinus")
            ->setParameter("dateMinus", date("Y-m-d",strtotime("-1 month")));
        if($dateDeb!=""){
            $querybuild->andWhere("so.dateHeureDebut > :dateD")
                ->setParameter("dateD",$dateDeb);
        }
        if($dateFin!=""){
            $querybuild->andWhere("so.dateHeureDebut < :dateF")
                ->setParameter("dateF",$dateFin);
        }

        if($past!="on") {
            $querybuild->andWhere("e.libelle != 'Passee'");
        }
        if($organisateur=="on" and $participateur=="on" and $nonparticipant=="on"){
            $querybuild->andWhere("(so.organisateur =:user ) or (so.organisateur !=:user and e.libelle!='Creee')");
        }
        if($organisateur!="on" and $nonparticipant=="on"){
            if($participateur!="on"){
                $querybuild->andWhere(":user NOT MEMBER OF so.participants");
            }
            $querybuild->andWhere("e.libelle !='Creee' and so.organisateur !=:user");
        }
        if($organisateur!="on" and $participateur=="on" and $nonparticipant!="on"){
            $querybuild->andWhere(":user MEMBER OF so.participants");
        }
        if($organisateur=="on" and $participateur=="on" and $nonparticipant!="on"){
            $querybuild->andWhere("(:user MEMBER OF so.participants) or (so.organisateur =:user )");
        }
        if($organisateur=="on" and $participateur!="on" and $nonparticipant!="on"){
            $querybuild->andWhere("so.organisateur =:user");
        }
        if($organisateur=="on" and $participateur!="on" and $nonparticipant=="on"){
            $querybuild->andWhere("(:user NOT MEMBER OF so.participants and e.libelle !='Creee') or so.organisateur = :user");
        }
        if($organisateur!="on" and $participateur!="on" and $nonparticipant!="on"){
            $querybuild->andWhere("e.libelle = 'Creee' and e.libelle != 'Creee'");
        }
        if($organisateur=="on" or $participateur=="on" or $nonparticipant=="on") {
            $querybuild->setParameter("user", $user);
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
