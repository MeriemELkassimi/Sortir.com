<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
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
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findFilteredSorties($oFilters, $oUser): array
    {
        $queryBuilder=$this->createQueryBuilder('sortie')
            ->andWhere('sortie.campus = :campus')
            ->setParameter('campus', $oFilters->getCampus())

            ->andWhere('(sortie.dateHeureDebut <= :date_fin AND sortie.dateHeureDebut >= :date_debut)')
            ->setParameter('date_fin', $oFilters->getDateFin())
            ->setParameter('date_debut', $oFilters->getDateDebut());

        /*if ($oFilters->isPassees()) {
            $queryBuilder
                ->andWhere('sortie.etat.id() = :etat_passee')
                ->setParameter('etat_passee', 'Passée');
        }*/


            //->setParameter('etat_passee', $oFilters->isPassees())

        $queryBuilder->orderBy('sortie.id', 'ASC')
            ->setMaxResults(10);
        return $queryBuilder->getQuery()->getResult();


           /* $this->createQueryBuilder('sortie')
            ->andWhere('sortie.campus = :campus')
            ->setParameter('campus', $oFilters->getCampus())

            ->andWhere('(sortie.dateHeureDebut <= :date_fin AND sortie.dateHeureDebut >= :date_debut)')
            ->setParameter('date_fin', $oFilters->getDateFin())
            ->setParameter('date_debut', $oFilters->getDateDebut())

            ->andWhere('(sortie.etat = :etat_passee )')
            ->setParameter('etat_passee', $oFilters->isPassees())

            ->orderBy('sortie.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();*/
    }



//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
