<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'orders')]
    // private Collection $products;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: OrderDetail::class)]
    private Collection $orderDetails;

    #[ORM\Column]
    private ?float $price = null;

    // #[ORM\OneToMany(mappedBy: 'orders', targetEntity: OrderDetail::class, orphanRemoval: true)]
    // private Collection $orderDetails;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        // $this->products = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    // /**
    //  * @return Collection<int, OrderDetail>
    //  */
    // public function getOrderDetails(): Collection
    // {
    //     return $this->orderDetails;
    // }

    // public function addOrderDetail(OrderDetail $orderDetail): self
    // {
    //     if (!$this->orderDetails->contains($orderDetail)) {
    //         $this->orderDetails->add($orderDetail);
    //         $orderDetail->setOrders($this);
    //     }

    //     return $this;
    // }

    // public function removeOrderDetail(OrderDetail $orderDetail): self
    // {
    //     if ($this->orderDetails->removeElement($orderDetail)) {
    //         // set the owning side to null (unless already changed)
    //         if ($orderDetail->getOrders() === $this) {
    //             $orderDetail->setOrders(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Product>
     */
    // public function getProducts(): Collection
    // {
    //     return $this->products;
    // }

    // public function addProduct(Product $product): self
    // {
    //     if (!$this->products->contains($product)) {
    //         $this->products->add($product);
    //     }

    //     return $this;
    // }

    // public function removeProduct(Product $product): self
    // {
    //     $this->products->removeElement($product);

    //     return $this;
    // }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setOrders($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getOrders() === $this) {
                $orderDetail->setOrders(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
