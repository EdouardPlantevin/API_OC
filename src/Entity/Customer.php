<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups("customer:read")]
    #[Assert\Length(min: 3)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(min: 3)]
    #[Groups("customer:read")]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email()]
    #[Groups("customer:read")]
    private $email;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Length(min: 10, minMessage: "Le numéro dois contenir au moins 10 caractères")]
    #[Groups("customer:read")]
    private $phone;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customers')]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
