<?php

namespace App\Entity;

use App\Repository\ProductoInversionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CuentaBancaria;
use App\Entity\FondoInversion;

#[ORM\Entity(repositoryClass: ProductoInversionRepository::class)]
class ProductoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numContrato = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $fechaApertura = null;

    #[ORM\ManyToOne(inversedBy: 'productosInversion')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CuentaBancaria $cuenta = null;

    #[ORM\OneToMany(mappedBy: 'productoInversion', targetEntity: FondoInversion::class, orphanRemoval: true)]
    private Collection $fondos;


    public function __construct()
    {
        $this->fondos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumContrato(): ?string
    {
        return $this->numContrato;
    }

    public function setNumContrato(string $numContrato): static
    {
        $this->numContrato = $numContrato;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFechaApertura(): ?\DateTimeInterface
    {
        return $this->fechaApertura;
    }

    public function setFechaApertura(\DateTimeInterface $fechaApertura): static
    {
        $this->fechaApertura = $fechaApertura;

        return $this;
    }

    public function getCuentaBancaria(): ?CuentaBancaria
    {
        return $this->cuenta;
    }

    public function setCuentaBancaria(?CuentaBancaria $cuenta): static
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    public function getFondos(): Collection
    {
        return $this->fondos;
    }

    public function addFondo(FondoInversion $fondo): static
    {
        if (!$this->fondos->contains($fondo)) {
            $this->fondos->add($fondo);
            $fondo->setProductoInversion($this);
        }

        return $this;
    }

    public function removeFondo(FondoInversion $fondo): static
    {
        if ($this->fondos->removeElement($fondo)) {
            if ($fondo->getProductoInversion() === $this) {
                $fondo->setProductoInversion(null);
            }
        }

        return $this;
    }


    /**
     * Suma el valor actual de mercado del último estado de cada fondo.
     */
    public function getTotalActualFondo(): float
    {
        $total = 0.0;
        foreach ($this->fondos as $fondo) {
            $ultimoEstado = $fondo->getUltimoEstado();
            if ($ultimoEstado !== null) {
                $total += $ultimoEstado->getValorActual() ?? 0.0;
            }
        }

        return $total;
    }

    public function getTotalInvertido(): float
    {
        $total = 0.0;
        foreach ($this->fondos as $fondo) {
            foreach ($fondo->getMovimientos() as $movimiento) {
                $total += $movimiento->getImporteNetoAportado() ?? 0.0;
            }
        }
        return $total;
    }
}