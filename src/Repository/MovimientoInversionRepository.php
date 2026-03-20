<?php

namespace App\Repository;

use App\Entity\MovimientoInversion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\FondoInversion;

/**
 * @extends ServiceEntityRepository<MovimientoInversion>
 */
class MovimientoInversionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovimientoInversion::class);
    }

    public function getTotalInvertido(User $user): float
    {
        $result = $this->createQueryBuilder('m')
            ->select('SUM(m.importeNetoAportado)')
            ->join('m.fondoInversion', 'f')
            ->join('f.productoInversion', 'p')
            ->join('p.cuenta', 'c')
            ->join('c.usuarios', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getTotalInvertidoAnio(User $user, int $anio): float
    {
        $desde = new \DateTime("$anio-01-01");
        $hasta = new \DateTime("$anio-12-31");

        $result = $this->createQueryBuilder('m')
            ->select('SUM(m.importeNetoAportado)')
            ->join('m.fondoInversion', 'f')
            ->join('f.productoInversion', 'p')
            ->join('p.cuenta', 'c')
            ->join('c.usuarios', 'u')
            ->where('u = :user')
            ->andWhere('m.fecha >= :desde')
            ->andWhere('m.fecha <= :hasta')
            ->setParameter('user', $user)
            ->setParameter('desde', $desde)
            ->setParameter('hasta', $hasta)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function getUltimasAportaciones(User $user, int $limit = 5): array
    {
        $fondos = $this->getEntityManager()->createQueryBuilder()
            ->select('f.id')
            ->from(FondoInversion::class, 'f')
            ->join('f.productoInversion', 'p')
            ->join('p.cuenta', 'c')
            ->join('c.usuarios', 'u')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult();

        $resultado = [];

        foreach ($fondos as $fondo) {
            $movimientos = $this->createQueryBuilder('m')
                ->join('m.fondoInversion', 'f')
                ->where('f.id = :fondoId')
                ->setParameter('fondoId', $fondo['id'])
                ->orderBy('m.fecha', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

            if (!empty($movimientos)) {
                $resultado[$fondo['id']] = $movimientos;
            }
        }

        return $resultado;
    }
}
