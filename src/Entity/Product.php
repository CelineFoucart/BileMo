<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["index"])]
    #[OA\Property(description: 'The unique identifier of the product.')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["index"])]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["index"])]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["index"])]
    #[OA\Property(type: 'string')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(["index"])]
    #[OA\Property(type: 'number', format:'float')]
    private ?string $price = null;

    #[ORM\Column]
    #[Groups(["index"])]
    #[OA\Property(type: 'string', format:'date-time')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["index"])]
    #[OA\Property(type: 'string', format:'date-time')]
    private ?\DateTimeInterface $updatedAt = null;

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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[Groups(["index"])]
    #[SerializedName('_links')]
    #[OA\Property(type:'object', description:'The product links', properties:[new OA\Property(property:'self', type:'string')])]
    public function getLinks(): array
    {
        return ['self' => ['href' => '/api/products/' . $this->getId()]];
    }
}
