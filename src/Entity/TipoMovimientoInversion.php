<?php

namespace App\Entity;

use App\Repository\TipoMovimientoInversionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TipoMovimientoInversionRepository::class)]
class TipoMovimientoInversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique:true)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'tipo', targetEntity: MovimientoInversion::class)]
    private Collection $movimientos;

    public function __construct()
    {
        $this->movimientos = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
}
