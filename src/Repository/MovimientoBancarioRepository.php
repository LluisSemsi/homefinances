<?php

namespace App\Repository;

use App\Entity\CuentaBancaria;
use App\Entity\MovimientoBancario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * @extends ServiceEntityRepository<MovimientoBancario>
 */
class MovimientoBancarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovimientoBancario::class);
    }

    public function obtenerBalancePeriodoUsuario(\DateTimeInterface $inicio, \DateTimeInterface $fin, int $userId): float {
        return (float) $this->createQueryBuilder('m')
        ->select('COALESCE(SUM(m.cantidad), 0)')
        ->join('m.cuenta', 'c')
        ->join('c.usuarios', 'u')
        ->where('m.fecha >= :inicio')
        ->andWhere('m.fecha < :fin')
        ->andWhere('u.id = :userId')
        ->setParameter('inicio', $inicio)
        ->setParameter('fin', $fin)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getSingleScalarResult();

    }


    public function obtenerIngresosTotalesPeriodoUsuario(\DateTimeInterface $inicio, \DateTimeInterface $fin, int $userId): float {
        return (float) $this->createQueryBuilder('m')
        ->select('COALESCE(SUM(m.cantidad), 0)')
        ->join('m.cuenta', 'c')
        ->join('c.usuarios', 'u')
        ->where('m.fecha BETWEEN :inicio AND :fin')
        ->andWhere('u.id = :userId')
        ->andWhere('m.cantidad > 0')
        ->setParameter('inicio', $inicio)
        ->setParameter('fin', $fin)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getSingleScalarResult();

    }

    public function getMovimientosBancariosSinCategorizar(PaginatorInterface $paginator,CuentaBancaria $cuenta, Request $request) 
    {

        $query = $this->createQueryBuilder('m')
            ->join('m.tipo', 't')
            ->where('m.cuenta = :cuenta')
            ->andWhere('t.nombre IN (:tipos)')
            ->setParameter('cuenta', $cuenta)
            ->setParameter('tipos', ['INGRESO', 'GASTO'])
            ->orderBy('m.fecha', 'DESC')
            ->getQuery();

        return $paginator->paginate($query, $request->query->getInt('page', 1), 20);

    }

    public function getMovimientosCuenta(PaginatorInterface $paginator, CuentaBancaria $cuenta, Request $request, ?\DateTime $desde, ?\DateTime $hasta)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.cuenta = :cuenta')
            ->setParameter('cuenta', $cuenta)
            ->orderBy('m.fecha', 'DESC');

        if ($desde) {
            $qb->andWhere('m.fecha >= :desde')->setParameter('desde', $desde);
        }
        if ($hasta) {
            $qb->andWhere('m.fecha <= :hasta')->setParameter('hasta', $hasta);
        }

        return $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 20);
    }

    public function getIngresosGastosUltimosXMes(CuentaBancaria $cuenta, int $meses): array
    {
        if($meses > 1)
            $desde = new \DateTime('-' . $meses . ' months');
        else
            $desde = new \DateTime('-1 month');

        return $this->createQueryBuilder('m')
            ->select(
            'SUBSTRING(m.fecha, 1, 7) as mes',
            'SUM(CASE WHEN m.cantidad > 0 THEN m.cantidad ELSE 0 END) as ingresos',
            'SUM(CASE WHEN m.cantidad < 0 THEN ABS(m.cantidad) ELSE 0 END) as gastos'
        )
            ->where('m.cuenta = :cuenta')
            ->andWhere('m.fecha >= :desde')
            ->setParameter('cuenta', $cuenta)
            ->setParameter('desde', $desde)
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getGastosPorCategoriaYMeses(CuentaBancaria $cuenta, int $meses): array
    {
        if($meses > 1)
            $desde = new \DateTime('-' . $meses . ' months');
        else
            $desde = new \DateTime('-1 month');

        return $this->createQueryBuilder('m')
            ->select('t.nombre as categoria', 'SUM(ABS(m.cantidad)) as total')
            ->join('m.tipo', 't')
            ->where('m.cuenta = :cuenta')
            ->andWhere('m.cantidad < 0')
            ->andWhere('m.fecha >= :desde')
            ->andWhere('t.nombre NOT IN (:excluidos)')
            ->setParameter('cuenta', $cuenta)
            ->setParameter('desde', $desde)
            ->setParameter('excluidos', ['INGRESO', 'GASTO'])
            ->groupBy('t.nombre')
            ->orderBy('total', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
    
}
