<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Repository\CartRepository;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{

    use CreatedAtTrait;
    
    #[ORM\Column]
    private ?int $qnty = null;

    // #[ORM\Id]
    // #[ORM\ManyToOne(inversedBy: 'carts')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Order $orders = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $products = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $users = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getQnty(): ?int
    {
        return $this->qnty;
    }

    public function setQnty(int $qnty): self
    {
        $this->qnty = $qnty;

        return $this;
    }

    // public function getOrders(): ?Order
    // {
    //     return $this->orders;
    // }

    // public function setOrders(?Order $orders): self
    // {
    //     $this->orders = $orders;

    //     return $this;
    // }    

    public function getProducts(): ?Product
    {
        return $this->products;
    }

    public function setProducts(?Product $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

}
