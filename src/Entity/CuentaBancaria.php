<?php

namespace App\Entity;

use App\Repository\CuentaBancariaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\MovimientoBancario;
use App\Entity\ProductoInversion;

#[ORM\Entity(repositoryClass: CuentaBancariaRepository::class)]
class CuentaBancaria
{
    #[ORM\Id]
    #[ORM\Column(length: 34)]
    private ?string $iban = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre_banco = null;

    #[ORM\Column(length: 100)]
    private ?string $titular = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $saldo = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'cuentasBancarias')]
    #[ORM\JoinTable(
        name: 'cuenta_bancaria_user',
        joinColumns: [
            new ORM\JoinColumn(name: 'cuenta_bancaria_iban', referencedColumnName: 'iban')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')
        ]
    )]
    private Collection $usuarios;

    #[ORM\OneToMany(mappedBy: 'cuenta', targetEntity: MovimientoBancario::class, orphanRemoval: true)]
    private Collection $movimientosBancarios;

    #[ORM\OneToMany(mappedBy: 'cuenta', targetEntity: ProductoInversion::class, orphanRemoval: true)]
    private Collection $productosInversion;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->movimientosBancarios = new ArrayCollection();
        $this->productosInversion = new ArrayCollection();
    }

    public function getIBAN(): ?string
    {
        return $this->iban;
    }

    public function setIBAN(string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getNombreBanco(): ?string
    {
        return $this->nombre_banco;
    }

    public function setNombreBanco(string $nombre_banco): static
    {
        $this->nombre_banco = $nombre_banco;

        return $this;
    }

    public function getTitular(): ?string
    {
        return $this->titular;
    }

    public function setTitular(string $titular): static
    {
        $this->titular = $titular;

        return $this;
    }

    public function getSaldo(): ?string
    {
        return $this->saldo;
    }

    public function setSaldo(string $saldo): static
    {
        $this->saldo = $saldo;

        return $this;
    }

    /** @return Collection<int, User> */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(User $user): self
    {
        if (!$this->usuarios->contains($user)) {
            $this->usuarios[] = $user;
        }
        return $this;
    }

    public function removeUsuario(User $user): self
    {
        $this->usuarios->removeElement($user);
        return $this;
    }

    public function getMovimientosBancarios(): Collection
    {
        return $this->movimientosBancarios;
    }

    public function addMovimientosBancarios(MovimientoBancario $movimientoBancario): self
    {
        if (!$this->movimientosBancarios->contains($movimientoBancario)) {
            $this->movimientosBancarios[] = $movimientoBancario;
            $movimientoBancario->setCuenta($this);
        }
        return $this;
    }

    public function removeGasto(MovimientoBancario $movimientoBancario): self
    {
        if ($this->movimientosBancarios->removeElement($movimientoBancario)) {
            if ($movimientoBancario->getCuenta() === $this) {
                $movimientoBancario->setCuenta(null);
            }
        }
        return $this;
    }

    public function getProductosInversion(): Collection
    {
        return $this->productosInversion;
    }

    public function addProductoInversion(ProductoInversion $producto): static
    {
        if (!$this->productosInversion->contains($producto)) {
            $this->productosInversion->add($producto);
            $producto->setCuentaBancaria($this);
        }

        return $this;
    }

    public function removeProductoInversion(ProductoInversion $producto): static
    {
        if ($this->productosInversion->removeElement($producto)) {
            // set the owning side to null (unless already changed)
            if ($producto->getCuentaBancaria() === $this) {
                $producto->setCuentaBancaria(null);
            }
        }

        return $this;
    }
}
