<?php

namespace App\Repository;

use App\Entity\ProductoInversion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\EstadoFondoInversion;

/**
 * @extends ServiceEntityRepository<ProductoInversion>
 */
class ProductoInversionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductoInversion::class);
    }

    public function getProductosConFondos(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.cuenta', 'c')
            ->join('c.usuarios', 'u')
            ->leftJoin('p.fondos', 'f')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getValorActualTotal(User $user): float
    {
        $productos = $this->getProductosConFondos($user);
        $total = 0.0;

        foreach ($productos as $producto) {
            $total += $producto->getTotalActualFondo();
        }

        return $total;
    }

    public function getEvolucionPortfolio(User $user): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select(
                'e.fecha as fecha',
                'SUM(e.valorActual) as valor_actual',
                'SUM(e.importeInvertido) as capital_invertido'
            )
            ->from(EstadoFondoInversion::class, 'e')
            ->join('e.fondoInversion', 'f')
            ->join('f.productoInversion', 'p')
            ->join('p.cuenta', 'c')
            ->join('c.usuarios', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->groupBy('e.fecha')
            ->orderBy('e.fecha', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
