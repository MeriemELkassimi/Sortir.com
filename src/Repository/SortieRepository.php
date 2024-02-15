<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     * @return Paginator Returns an array of Sortie objects
     */
    public function findFilteredSorties($oFilters, $oUser): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('sortie');

        if ($oFilters->getCampus()) {
            $queryBuilder
                ->andWhere('sortie.campus = :campus')
                ->setParameter('campus', $oFilters->getCampus());
        }

        if ($oFilters->getDateDebut() and $oFilters->getDateFin()) {
            $queryBuilder
                ->andWhere('(sortie.dateHeureDebut <= :date_fin AND sortie.dateHeureDebut >= :date_debut)')
                ->setParameter('date_fin', $oFilters->getDateFin())
                ->setParameter('date_debut', $oFilters->getDateDebut());
        }

        if ($oFilters->isPassees()) {
            $queryBuilder
                ->andWhere('sortie.etat = :etat_passee')
                ->setParameter('etat_passee', 5);
        }

        if ($oFilters->isOrganisateur()) {
            $queryBuilder
                ->andWhere('sortie.organisateur = :orga')
                ->setParameter('orga', $oUser);
        }

        if ($oFilters->isInscrit()) {
            $queryBuilder
                ->leftJoin('sortie.participants', 'participant')
                ->andWhere('participant.id = :insc')
                ->setParameter('insc', $oUser);
        }


        if ($oFilters->isPasInscrit()) {
            $subQueryBuilder = $this->createQueryBuilder('sort');
            $subQueryBuilder
                ->select('s.id')
                ->from('App\Entity\Sortie', 's')
                ->join('s.participants', 'p')
                ->where('p.id = :userId');

            $queryBuilder
                ->leftJoin('sortie.participants', 'participant')
                ->andWhere($queryBuilder->expr()->notIn('sortie.id', $subQueryBuilder->getDQL()))
                ->setParameter('userId', $oUser);

        }

        $queryBuilder->orderBy('sortie.id', 'ASC')
            ->setMaxResults(10);

        $paginator = new Paginator($queryBuilder);
        return $paginator;

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