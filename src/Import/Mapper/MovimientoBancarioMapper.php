<?php

namespace App\Import\Mapper;

use App\Entity\MovimientoBancario;
use App\Entity\CuentaBancaria;
use App\Import\DTO\MovimientoBancarioDTO;
use App\Repository\TipoMovimientoBancarioRepository;

class MovimientoBancarioMapper
{
    public function __construct(
        private TipoMovimientoBancarioRepository $tipoRepo
    ) {}

    public function map(MovimientoBancarioDTO $dto, CuentaBancaria $cuenta): MovimientoBancario
    {
        $mov = new MovimientoBancario();
        $mov->setFecha($dto->fecha);
        $mov->setCantidad($dto->cantidad);
        $mov->setConcepto($dto->concepto);
        $mov->setSaldoActual($dto->saldoActual);
        $mov->setCuenta($cuenta);

        $hash = md5(
            $dto->fecha->format('Y-m-d') .
            $dto->cantidad .
            $dto->concepto .
            $dto->saldoActual
        );

        $mov->setHash($hash);

        $tipo = $this->tipoRepo->findOneBy(['nombre' => $dto->tipoNombre]);
        if (!$tipo) {
            throw new \RuntimeException('TipoMovimientoBancario no encontrado: '.$dto->tipoNombre);
        }
        $mov->setTipo($tipo);

        return $mov;
    }
}
