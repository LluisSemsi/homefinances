<?php

namespace App\Entity;

use App\Repository\FondoInversionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ProductoInversion;
use App\Entity\MovimientoInversion;

#[ORM\Entity(repositoryClass: FondoInversionRepository::class)]
class FondoInversion
{
    #[ORM\Id]
    #[ORM\Column(length: 12)]
    private ?string $isin = null;

    #[ORM\Column]
    private ?\DateTime $fecha_apertura = null;

    #[ORM\Column(length: 45)]
    private ?string $periodicidad = null;

    #[ORM\Column(length: 45)]
    private ?string $duracion = null;

    #[ORM\Column]
    private ?int $num_participaciones = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio_medio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ultimo_precio = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $diferencia = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $importe_invertido = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dividendos = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $valor_actual = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $diferencia_total = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $diferencia_total_percent = null;

    #[ORM\ManyToOne(inversedBy: 'fondos')]
    #[ORM\JoinColumn(name: 'producto_inversion', referencedColumnName: 'num_contrato', nullable: false)]
    private ?ProductoInversion $producto_inversion = null;

    /**
     * @var Collection<int, MovimientoInversion>
     */
    #[ORM\OneToMany(targetEntity: MovimientoInversion::class, mappedBy: 'fondo_inversion')]
    private Collection $movimientos;

    public function __construct()
    {
        $this->movimientos = new ArrayCollection();
    }

    public function getISIN(): ?string
    {
        return $this->isin;
    }

    public function setISIN(int $isin): static
    {
        $this->isin = $isin;

        return $this;
    }

    public function getFechaApertura(): ?\DateTime
    {
        return $this->fecha_apertura;
    }

    public function setFechaApertura(\DateTime $fecha_apertura): static
    {
        $this->fecha_apertura = $fecha_apertura;

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

    public function getNumParticipaciones(): ?int
    {
        return $this->num_participaciones;
    }

    public function setNumParticipaciones(int $num_participaciones): static
    {
        $this->num_participaciones = $num_participaciones;

        return $this;
    }

    public function getPrecioMedio(): ?string
    {
        return $this->precio_medio;
    }

    public function setPrecioMedio(string $precio_medio): static
    {
        $this->precio_medio = $precio_medio;

        return $this;
    }

    public function getUltimoPrecio(): ?string
    {
        return $this->ultimo_precio;
    }

    public function setUltimoPrecio(string $ultimo_precio): static
    {
        $this->ultimo_precio = $ultimo_precio;

        return $this;
    }

    public function getDiferencia(): ?string
    {
        return $this->diferencia;
    }

    public function setDiferencia(string $diferencia): static
    {
        $this->diferencia = $diferencia;

        return $this;
    }

    public function getImporteInvertido(): ?string
    {
        return $this->importe_invertido;
    }

    public function setImporteInvertido(string $importe_invertido): static
    {
        $this->importe_invertido = $importe_invertido;

        return $this;
    }

    public function getDividendos(): ?string
    {
        return $this->dividendos;
    }

    public function setDividendos(string $dividendos): static
    {
        $this->dividendos = $dividendos;

        return $this;
    }

    public function getValorActual(): ?string
    {
        return $this->valor_actual;
    }

    public function setValorActual(string $valor_actual): static
    {
        $this->valor_actual = $valor_actual;

        return $this;
    }

    public function getDiferenciaTotal(): ?string
    {
        return $this->diferencia_total;
    }

    public function setDiferenciaTotal(string $diferencia_total): static
    {
        $this->diferencia_total = $diferencia_total;

        return $this;
    }

    public function getDiferenciaTotalPercent(): ?string
    {
        return $this->diferencia_total_percent;
    }

    public function setDiferenciaTotalPercent(string $diferencia_total_percent): static
    {
        $this->diferencia_total_percent = $diferencia_total_percent;

        return $this;
    }

    public function getProductoInversion(): ?ProductoInversion
    {
        return $this->producto_inversion;
    }

    public function setProductoInversion(?ProductoInversion $producto_inversion): static
    {
        $this->producto_inversion = $producto_inversion;

        return $this;
    }

    /**
     * @return Collection<int, MovimientoInversion>
     */
    public function getMovimientos(): Collection
    {
        return $this->movimientos;
    }

    public function addMovimiento(MovimientoInversion $movimiento): static
    {
        if (!$this->movimientos->contains($movimiento)) {
            $this->movimientos->add($movimiento);
            $movimiento->setFondoInversion($this);
        }

        return $this;
    }

    public function removeMovimiento(MovimientoInversion $movimiento): static
    {
        if ($this->movimientos->removeElement($movimiento)) {
            // set the owning side to null (unless already changed)
            if ($movimiento->getFondoInversion() === $this) {
                $movimiento->setFondoInversion(null);
            }
        }

        return $this;
    }
}
