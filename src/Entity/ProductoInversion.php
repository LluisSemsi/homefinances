<?php

namespace App\Entity;

use App\Repository\ProductoInversionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CuentaBancaria;
use App\Entity\FondoInversion;

#[ORM\Entity(repositoryClass: ProductoInversionRepository::class)]
class ProductoInversion
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $num_contrato = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_actual_fondo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_invertido = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $diferencia_total = null;

    #[ORM\Column]
    private ?\DateTime $fecha_apertura = null;

    #[ORM\ManyToOne(inversedBy: 'productosInversion')]
    #[ORM\JoinColumn(name: 'cuenta_iban', referencedColumnName: 'iban', nullable: false)]
    private ?CuentaBancaria $cuenta = null;

    #[ORM\OneToMany(mappedBy: 'producto_inversion', targetEntity: FondoInversion::class, orphanRemoval: true)]
    private Collection $fondos;

    public function __construct()
    {
        $this->fondos = new ArrayCollection();
    }

    public function getNumContrato(): ?int
    {
        return $this->num_contrato;
    }

    public function setNumContrato(int $num_contrato): static
    {
        $this->num_contrato = $num_contrato;

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

    public function getTotalActualFondo(): ?string
    {
        return $this->total_actual_fondo;
    }

    public function setTotalActualFondo(string $total_actual_fondo): static
    {
        $this->total_actual_fondo = $total_actual_fondo;

        return $this;
    }

    public function getTotalInvertido(): ?string
    {
        return $this->total_invertido;
    }

    public function setTotalInvertido(string $total_invertido): static
    {
        $this->total_invertido = $total_invertido;

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

    public function getFechaApertura(): ?\DateTime
    {
        return $this->fecha_apertura;
    }

    public function setFechaApertura(\DateTime $fecha_apertura): static
    {
        $this->fecha_apertura = $fecha_apertura;

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
}
