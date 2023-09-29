<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[UniqueEntity(fields: ['username'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['index'])]
    #[OA\Property(description: 'The unique identifier of the customer.')]
    /**
     * @var integer|null The unique identifier of the customer
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['index'])]
    #[OA\Property(type: 'string', minLength: 1, maxLength: 255, description: 'The customer fistname.')]
    /**
     * @var string|null The customer fistname
     */
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['index'])]
    #[OA\Property(type: 'string', minLength: 1, maxLength: 255, description: 'The customer lastname.')]
    /**
     * @var string|null The customer lastname
     */
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['index'])]
    #[OA\Property(type: 'string', minLength: 1, maxLength: 255, description: 'The customer email.')]
    /**
     * @var string|null The customer email
     */
    private ?string $email = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 180)]
    #[Groups(['index'])]
    #[OA\Property(type: 'string', minLength: 1, maxLength: 180, description: 'The customer usename.')]
    /**
     * @var string|null The customer usename
     */
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * @var Client|null The customer client
     */
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    #[Groups(['index'])]
    #[SerializedName('_links')]
    #[OA\Property(type: 'object', description: 'The customer links', properties: [new OA\Property(property: 'self', type: 'string'), new OA\Property(property: 'delete', type: 'string')])]
    public function getLinks(): array
    {
        return [
            'self' => ['href' => '/api/customers/'.$this->getId()],
            'delete' => ['href' => '/api/customers/'.$this->getId()],
        ];
    }
}
