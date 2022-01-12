<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedRepository::class)
 */
class Ordered
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numeroOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="ordereds")
     */
    private $commune;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $statusOrdered;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $TotalOrdered;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="ordereds")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=OrderedDetail::class, mappedBy="ordered")
     */
    private $orderedDetails;

    public function __construct()
    {
        $this->orderedDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getNumeroOrder(): ?string
    {
        return $this->numeroOrder;
    }

    public function setNumeroOrder(string $numeroOrder): self
    {
        $this->numeroOrder = $numeroOrder;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

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

    public function getStatusOrdered(): ?int
    {
        return $this->statusOrdered;
    }

    public function setStatusOrdered(int $statusOrdered): self
    {
        $this->statusOrdered = $statusOrdered;

        return $this;
    }

    public function getTotalOrdered(): ?string
    {
        return $this->TotalOrdered;
    }

    public function setTotalOrdered(string $TotalOrdered): self
    {
        $this->TotalOrdered = $TotalOrdered;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|OrderedDetail[]
     */
    public function getOrderedDetails(): Collection
    {
        return $this->orderedDetails;
    }

    public function addOrderedDetail(OrderedDetail $orderedDetail): self
    {
        if (!$this->orderedDetails->contains($orderedDetail)) {
            $this->orderedDetails[] = $orderedDetail;
            $orderedDetail->setOrdered($this);
        }

        return $this;
    }

    public function removeOrderedDetail(OrderedDetail $orderedDetail): self
    {
        if ($this->orderedDetails->removeElement($orderedDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderedDetail->getOrdered() === $this) {
                $orderedDetail->setOrdered(null);
            }
        }

        return $this;
    }
}
