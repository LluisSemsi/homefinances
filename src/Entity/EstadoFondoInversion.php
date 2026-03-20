<?php

namespace App\Entity;

use App\Repository\EstadoFondoInversionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstadoFondoInversionRepository::class)]
class EstadoFondoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 45)]
    private ?string $periodicidad = null;

    #[ORM\Column(length: 45)]
    private ?string $duracion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $numParticipaciones = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precioMedio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ultimoPrecio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $diferencia = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importeInvertido = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dividendos = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $valorActual = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $diferenciaTotal = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 4)]
    private ?string $diferenciaTotalPercent = null;

    #[ORM\ManyToOne(inversedBy: 'estados')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FondoInversion $fondoInversion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPeriodicidad(): ?string
    {
        return $this->periodicidad;
    }

    public function setPeriodicidad(string $periodicidad): static
    {
        $this->periodicidad = $periodicidad;

        return $this;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(string $duracion): static
    {
        $this->duracion = $duracion;

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

    public function getPrecioMedio(): ?float
    {
        return $this->precioMedio !== null ? (float) $this->precioMedio : null;
    }

    public function setPrecioMedio(float|string $precioMedio): static
    {
        $this->precioMedio = (string) $precioMedio;

        return $this;
    }

    public function getUltimoPrecio(): ?float
    {
        return $this->ultimoPrecio !== null ? (float) $this->ultimoPrecio : null;
    }

    public function setUltimoPrecio(float|string $ultimoPrecio): static
    {
        $this->ultimoPrecio = (string) $ultimoPrecio;

        return $this;
    }

    public function getDiferencia(): ?float
    {
        return $this->diferencia !== null ? (float) $this->diferencia : null;
    }

    public function setDiferencia(float|string $diferencia): static
    {
        $this->diferencia = (string) $diferencia;

        return $this;
    }

    public function getImporteInvertido(): ?float
    {
        return $this->importeInvertido !== null ? (float) $this->importeInvertido : null;
    }

    public function setImporteInvertido(float|string $importeInvertido): static
    {
        $this->importeInvertido = (string) $importeInvertido;

        return $this;
    }

    public function getDividendos(): ?float
    {
        return $this->dividendos !== null ? (float) $this->dividendos : null;
    }

    public function setDividendos(float|string $dividendos): static
    {
        $this->dividendos = (string) $dividendos;

        return $this;
    }

    public function getValorActual(): ?float
    {
        return $this->valorActual !== null ? (float) $this->valorActual : null;
    }

    public function setValorActual(float|string $valorActual): static
    {
        $this->valorActual = (string) $valorActual;

        return $this;
    }

    public function getDiferenciaTotal(): ?float
    {
        return $this->diferenciaTotal !== null ? (float) $this->diferenciaTotal : null;
    }

    public function setDiferenciaTotal(float|string $diferenciaTotal): static
    {
        $this->diferenciaTotal = (string) $diferenciaTotal;

        return $this;
    }

    public function getDiferenciaTotalPercent(): ?float
    {
        return $this->diferenciaTotalPercent !== null ? (float) $this->diferenciaTotalPercent : null;
    }

    public function setDiferenciaTotalPercent(float|string $diferenciaTotalPercent): static
    {
        $this->diferenciaTotalPercent = (string) $diferenciaTotalPercent;

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