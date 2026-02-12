<?php

namespace App\Entity;

use App\Repository\MovimientoBancarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CuentaBancaria;

#[ORM\Entity(repositoryClass: MovimientoBancarioRepository::class)]
class MovimientoBancario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $cantidad = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $concepto = null;

    #[ORM\Column]
    private ?\DateTime $fecha = null;

    #[ORM\ManyToOne(inversedBy: 'movimientosBancarios')]
    #[ORM\JoinColumn(name: 'cuenta_iban', referencedColumnName: 'iban', nullable: false)]
    private ?CuentaBancaria $cuenta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(string $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getConcepto(): ?string
    {
        return $this->concepto;
    }

    public function setConcepto(?string $concepto): static
    {
        $this->concepto = $concepto;

        return $this;
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

    public function getCuenta(): ?string
    {
        return $this->cuenta;
    }

    public function setCuenta(?CuentaBancaria $cuenta): static
    {
        $this->cuenta = $cuenta;

        return $this;
    }
}
