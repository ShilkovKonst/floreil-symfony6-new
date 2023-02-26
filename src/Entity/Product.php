<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\SlugTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use CreatedAtTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $commonName = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $size = null;

    #[ORM\Column(length: 255)]
    private ?string $foliage = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $watering = null;

    #[ORM\Column(length: 255)]
    private ?string $bloom = null;

    #[ORM\Column]
    private ?bool $isFragrantBloom = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $inStockQnty = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: Cart::class, orphanRemoval: true)]
    private Collection $carts;

    #[ORM\Column]
    private ?bool $isResistedToCold = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?int $coldResistance = null;

    // #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products')]
    // private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'products', targetEntity: OrderDetail::class)]
    private Collection $orderDetails;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        // $this->orders = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(string $commonName): self
    {
        $this->commonName = $commonName;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getFoliage(): ?string
    {
        return $this->foliage;
    }

    public function setFoliage(string $foliage): self
    {
        $this->foliage = $foliage;

        return $this;
    }

    public function getWatering(): ?string
    {
        return $this->watering;
    }

    public function setWatering(string $watering): self
    {
        $this->watering = $watering;

        return $this;
    }

    public function getBloom(): ?string
    {
        return $this->bloom;
    }

    public function setBloom(string $bloom): self
    {
        $this->bloom = $bloom;

        return $this;
    }

    public function isIsFragrantBloom(): ?bool
    {
        return $this->isFragrantBloom;
    }

    public function setIsFragrantBloom(bool $isFragrantBloom): self
    {
        $this->isFragrantBloom = $isFragrantBloom;

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

    public function getinStockQnty(): ?int
    {
        return $this->inStockQnty;
    }

    public function setinStockQnty(int $inStockQnty): self
    {
        $this->inStockQnty = $inStockQnty;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setProducts($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getProducts() === $this) {
                $cart->setProducts(null);
            }
        }

        return $this;
    }

    public function isIsResistedToCold(): ?bool
    {
        return $this->isResistedToCold;
    }

    public function setIsResistedToCold(bool $isResistedToCold): self
    {
        $this->isResistedToCold = $isResistedToCold;

        return $this;
    }

    public function getColdResistance(): ?int
    {
        return $this->coldResistance;
    }

    public function setColdResistance(int $coldResistance): self
    {
        $this->coldResistance = $coldResistance;

        return $this;
    }

    // /**
    //  * @return Collection<int, Order>
    //  */
    // public function getOrders(): Collection
    // {
    //     return $this->orders;
    // }

    // public function addOrder(Order $order): self
    // {
    //     if (!$this->orders->contains($order)) {
    //         $this->orders->add($order);
    //         $order->addProduct($this);
    //     }

    //     return $this;
    // }

    // public function removeOrder(Order $order): self
    // {
    //     if ($this->orders->removeElement($order)) {
    //         $order->removeProduct($this);
    //     }

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
            $orderDetail->setProducts($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProducts() === $this) {
                $orderDetail->setProducts(null);
            }
        }

        return $this;
    }
}
