<?php

namespace App\Entity;

use App\Repository\MovimientoInversionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovimientoInversionRepository::class)]
class MovimientoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $numParticipaciones = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importeIva = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importeNetoAportado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importeRetencion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $comisionMovimiento = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importeBrutoAportado = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $precioUnitario = null;

    #[ORM\ManyToOne(inversedBy: 'movimientos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TipoMovimientoInversion $tipo = null;

    #[ORM\ManyToOne(inversedBy: 'movimientos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FondoInversion $fondoInversion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeImmutable
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeImmutable $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getNumParticipaciones(): ?float
    {
        return $this->numParticipaciones !== null ? (float) $this->numParticipaciones : null;
    }

    public function setNumParticipaciones(float|string $numParticipaciones): static
    {
        $this->numParticipaciones = (string) $numParticipaciones;

        return $this;
    }

    public function getImporteIva(): ?float
    {
        return $this->importeIva !== null ? (float) $this->importeIva : null;
    }

    public function setImporteIva(float|string $importeIva): static
    {
        $this->importeIva = (string) $importeIva;

        return $this;
    }

    public function getImporteNetoAportado(): ?float
    {
        return $this->importeNetoAportado !== null ? (float) $this->importeNetoAportado : null;
    }

    public function setImporteNetoAportado(float|string $importeNetoAportado): static
    {
        $this->importeNetoAportado = (string) $importeNetoAportado;

        return $this;
    }

    public function getImporteRetencion(): ?float
    {
        return $this->importeRetencion !== null ? (float) $this->importeRetencion : null;
    }

    public function setImporteRetencion(float|string $importeRetencion): static
    {
        $this->importeRetencion = (string) $importeRetencion;

        return $this;
    }

    public function getComisionMovimiento(): ?float
    {
        return $this->comisionMovimiento !== null ? (float) $this->comisionMovimiento : null;
    }

    public function setComisionMovimiento(float|string $comisionMovimiento): static
    {
        $this->comisionMovimiento = (string) $comisionMovimiento;

        return $this;
    }

    public function getImporteBrutoAportado(): ?float
    {
        return $this->importeBrutoAportado !== null ? (float) $this->importeBrutoAportado : null;
    }

    public function setImporteBrutoAportado(float|string $importeBrutoAportado): static
    {
        $this->importeBrutoAportado = (string) $importeBrutoAportado;

        return $this;
    }

    public function getPrecioUnitario(): ?float
    {
        return $this->precioUnitario !== null ? (float) $this->precioUnitario : null;
    }

    public function setPrecioUnitario(float|string $precioUnitario): static
    {
        $this->precioUnitario = (string) $precioUnitario;

        return $this;
    }

    public function getTipo(): ?TipoMovimientoInversion
    {
        return $this->tipo;
    }

    public function setTipo(TipoMovimientoInversion $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getFondoInversion(): ?FondoInversion
    {
        return $this->fondoInversion;
    }

    public function setFondoInversion(?FondoInversion $fondoInversion): static
    {
        $this->fondoInversion = $fondoInversion;

        return $this;
    }

}