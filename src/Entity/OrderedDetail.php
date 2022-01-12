<?php

namespace App\Entity;

use App\Repository\OrderedDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderedDetailRepository::class)
 */
class OrderedDetail
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
     * @ORM\Column(type="datetime")
     */
    private $dateOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="orderedDetails")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Ordered::class, inversedBy="orderedDetails")
     */
    private $ordered;

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

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->dateOrder;
    }

    public function setDateOrder(\DateTimeInterface $dateOrder): self
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getOrdered(): ?Ordered
    {
        return $this->ordered;
    }

    public function setOrdered(?Ordered $ordered): self
    {
        $this->ordered = $ordered;

        return $this;
    }
}
