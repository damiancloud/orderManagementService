<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use DateTime;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTime $date = null;

     /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\JoinTable(name="product_order",
     *      joinColumns={@ORM\JoinColumn(name="order_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")}
     * )
     */
    private ArrayCollection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    #[ORM\PrePersist]
    public function setDate(): void
    {
        $this->date = new DateTime();
    }

    /**
     * Add a product to the order.
     *
     * @param Product $product
     */
    public function addProduct(Products $product): void
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
    }

    /**
     * @return Collection|Products[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
}
