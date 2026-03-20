<?php

namespace App\Entity;

use App\Repository\FondoInversionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\EstadoFondoInversion;

#[ORM\Entity(repositoryClass: FondoInversionRepository::class)]
class FondoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12, unique: true)]
    private ?string $isin = null;

    #[ORM\Column(length: 150)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'fondos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductoInversion $productoInversion = null;

    /**
     * @var Collection<int, EstadoFondoInversion>
     */
    #[ORM\OneToMany(targetEntity: EstadoFondoInversion::class, mappedBy: 'fondoInversion', orphanRemoval: true)]
    private Collection $estados;

    #[ORM\OneToMany(targetEntity: MovimientoInversion::class, mappedBy: 'fondoInversion')]
    private Collection $movimientos;


    public function __construct()
    {
        $this->estados = new ArrayCollection();
        $this->movimientos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsin(): ?string
    {
        return $this->isin;
    }

    public function setIsin(string $isin): static
    {
        $this->isin = $isin;

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

    public function getProductoInversion(): ?ProductoInversion
    {
        return $this->productoInversion;
    }

    public function setProductoInversion(?ProductoInversion $productoInversion): static
    {
        $this->productoInversion = $productoInversion;

        return $this;
    }

    /**
     * @return Collection<int, EstadoFondoInversion>
     */
    public function getEstados(): Collection
    {
        return $this->estados;
    }

    public function addEstado(EstadoFondoInversion $estado): static
    {
        if (!$this->estados->contains($estado)) {
            $this->estados->add($estado);
            $estado->setFondoInversion($this);
        }

        return $this;
    }

    public function removeEstado(EstadoFondoInversion $estado): static
    {
        if ($this->estados->removeElement($estado)) {
            if ($estado->getFondoInversion() === $this) {
                $estado->setFondoInversion(null);
            }
        }

        return $this;
    }

    /**
     * Devuelve el estado más reciente del fondo.
     */
    public function getUltimoEstado(): ?EstadoFondoInversion
    {
        if ($this->estados->isEmpty()) {
            return null;
        }

        $estados = $this->estados->toArray();
        usort($estados, fn($a, $b) => $b->getFecha() <=> $a->getFecha());

        return $estados[0];
    }

}