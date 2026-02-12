<?php

namespace App\Entity;

use App\Repository\MovimientoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovimientoInversionRepository::class)]
class MovimientoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $fecha = null;

    #[ORM\Column(length: 20)]
    private ?string $tipo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $num_participaciones = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe_iva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe_neto_aportado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe_retencion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $comision_movimiento = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe_bruto_aportado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $precio_unitario = null;

    #[ORM\ManyToOne(inversedBy: 'movimientos')]
    #[ORM\JoinColumn(name: 'fondo_inversion', referencedColumnName: 'isin', nullable: false)]
    private ?FondoInversion $fondo_inversion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getNumParticipaciones(): ?string
    {
        return $this->num_participaciones;
    }

    public function setNumParticipaciones(string $num_participaciones): static
    {
        $this->num_participaciones = $num_participaciones;

        return $this;
    }

    public function getImporteIva(): ?string
    {
        return $this->importe_iva;
    }

    public function setImporteIva(string $importe_iva): static
    {
        $this->importe_iva = $importe_iva;

        return $this;
    }

    public function getImporteNetoAportado(): ?string
    {
        return $this->importe_neto_aportado;
    }

    public function setImporteNetoAportado(string $importe_neto_aportado): static
    {
        $this->importe_neto_aportado = $importe_neto_aportado;

        return $this;
    }

    public function getImporteRetencion(): ?string
    {
        return $this->importe_retencion;
    }

    public function setImporteRetencion(string $importe_retencion): static
    {
        $this->importe_retencion = $importe_retencion;

        return $this;
    }

    public function getComisionMovimiento(): ?string
    {
        return $this->comision_movimiento;
    }

    public function setComisionMovimiento(string $comision_movimiento): static
    {
        $this->comision_movimiento = $comision_movimiento;

        return $this;
    }

    public function getImporteBrutoAportado(): ?string
    {
        return $this->importe_bruto_aportado;
    }

    public function setImporteBrutoAportado(string $importe_bruto_aportado): static
    {
        $this->importe_bruto_aportado = $importe_bruto_aportado;

        return $this;
    }

    public function getPrecioUnitario(): ?string
    {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario(string $precio_unitario): static
    {
        $this->precio_unitario = $precio_unitario;

        return $this;
    }

    public function getFondoInversion(): ?FondoInversion
    {
        return $this->fondo_inversion;
    }

    public function setFondoInversion(?FondoInversion $fondo_inversion): static
    {
        $this->fondo_inversion = $fondo_inversion;

        return $this;
    }
}
