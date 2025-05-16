<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function countOrdersByDate(?string $startDate, ?string $endDate): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.dateCommande AS date')
            ->orderBy('c.dateCommande', 'ASC');

        if ($startDate && $endDate) {
            $qb->andWhere('c.dateCommande BETWEEN :start AND :end')
                ->setParameter('start', new \DateTime($startDate))
                ->setParameter('end', new \DateTime($endDate));
        }

        $results = $qb->getQuery()->getResult();

        // Regroupement en PHP par date (format YYYY-MM-DD)
        $grouped = [];
        foreach ($results as $row) {
            $dateKey = $row['date']->format('Y-m-d');
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = 0;
            }
            $grouped[$dateKey]++;
        }

        $final = [];
        foreach ($grouped as $date => $total) {
            $final[] = ['date' => $date, 'total' => $total];
        }

        return $final;
    }

    //    /**
    //     * @return Commande[] Returns an array of Commande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // src/Repository/CommandeRepository.php
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
