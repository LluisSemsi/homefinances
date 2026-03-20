<?php

namespace App\Import\DTO;

class MovimientoBancarioDTO
{
    public \DateTime $fecha;
    public float $cantidad;
    public ?string $concepto = null;
    public float $saldoActual;
    public string $tipoNombre; // p.ej. "INGRESO", "GASTO", etc.
}
