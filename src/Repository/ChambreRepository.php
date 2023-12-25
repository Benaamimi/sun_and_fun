<?php

namespace App\Repository;

use App\Entity\Chambre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chambre>
 *
 * @method Chambre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chambre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chambre[]    findAll()
 * @method Chambre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambre::class);
    }


public function findChambresNonReservees()
{
    return $this->createQueryBuilder('c')
        ->leftJoin('c.reservations', 'r')
        ->where('r.id IS NULL')
        ->andWhere('c.isDisponible = :isDisponible')
        ->setParameter('isDisponible', true)
        ->orderBy('c.titre', 'ASC')
        ->getQuery()
        ->getResult();
}


public function findChambresDisponibles()
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.isDisponible = :isDisponible')
        ->setParameter('isDisponible', true)
        ->orderBy('c.titre', 'ASC')
        ->getQuery()
        ->getResult();
}



// public function findChambreDisponible()
// {
//  $queryBuilder = $this->createQueryBuilder('c');

//  // Sélectionnez les chambres qui n'ont pas de réservations
//  $queryBuilder->leftJoin('c.reservations', 'r');
//  $queryBuilder->where($queryBuilder->expr()->isNull('r.id'));

//  return $queryBuilder->getQuery()->getResult();
// }



//    public function findOneBySomeField($value): ?Chambre
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
