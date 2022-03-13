<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommuneRepository::class)
 */
class Commune
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $tarif;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $montantMax;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Ordered::class, mappedBy="commune")
     */
    private $ordereds;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $archived;

    public function __construct()
    {
        $this->ordereds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTarif(): ?string
    {
        return $this->tarif;
    }

    public function setTarif(string $tarif): self
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getMontantMax(): ?string
    {
        return $this->montantMax;
    }

    public function setMontantMax(string $montantMax): self
    {
        $this->montantMax = $montantMax;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Ordered[]
     */
    public function getOrdereds(): Collection
    {
        return $this->ordereds;
    }

    public function addOrdered(Ordered $ordered): self
    {
        if (!$this->ordereds->contains($ordered)) {
            $this->ordereds[] = $ordered;
            $ordered->setCommune($this);
        }

        return $this;
    }

    public function removeOrdered(Ordered $ordered): self
    {
        if ($this->ordereds->removeElement($ordered)) {
            // set the owning side to null (unless already changed)
            if ($ordered->getCommune() === $this) {
                $ordered->setCommune(null);
            }
        }

        return $this;
    }

    public function getArchived(): ?int
    {
        return $this->archived;
    }

    public function setArchived(?int $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
