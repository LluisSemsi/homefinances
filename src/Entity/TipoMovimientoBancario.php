<?php

namespace App\Entity;

use App\Repository\TipoMovimientoBancarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipoMovimientoBancarioRepository::class)]
class TipoMovimientoBancario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, MovimientoBancario>
     */
    #[ORM\OneToMany(targetEntity: MovimientoBancario::class, mappedBy: 'tipo')]
    private Collection $movimientosBancarios;

    public function __construct()
    {
        $this->movimientosBancarios = new ArrayCollection();
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

    /**
     * @return Collection<int, MovimientoBancario>
     */
    public function getMovimientosBancarios(): Collection
    {
        return $this->movimientosBancarios;
    }

    public function addMovimientosBancario(MovimientoBancario $movimientosBancario): static
    {
        if (!$this->movimientosBancarios->contains($movimientosBancario)) {
            $this->movimientosBancarios->add($movimientosBancario);
            $movimientosBancario->setTipo($this);
        }

        return $this;
    }

    public function removeMovimientosBancario(MovimientoBancario $movimientosBancario): static
    {
        if ($this->movimientosBancarios->removeElement($movimientosBancario)) {
            // set the owning side to null (unless already changed)
            if ($movimientosBancario->getTipo() === $this) {
                $movimientosBancario->setTipo(null);
            }
        }

        return $this;
    }
}
