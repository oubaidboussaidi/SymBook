<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }
    public function findMostSoldBook(?string $startDate, ?string $endDate): ?array
    {
        $qb = $this->createQueryBuilder('lc')
            ->select('l.titre, SUM(lc.quantite) AS total')
            ->join('lc.livre', 'l')
            ->join('lc.commande', 'c')
            ->groupBy('l.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(1);

        if ($startDate && $endDate) {
            $qb->andWhere('c.dateCommande BETWEEN :start AND :end')
                ->setParameter('start', new \DateTime($startDate))
                ->setParameter('end', new \DateTime($endDate));
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    //    /**
    //     * @return LigneCommande[] Returns an array of LigneCommande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LigneCommande
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
