<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?\DateTime $checkingAt = null;

    #[ORM\Column]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?\DateTime $checkoutAt = null;

    #[ORM\Column]
    private ?int $prixTotal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    #[Assert\Positive( message: "Le nombre ne peut pas être négatif")]
    #[Assert\LessThanOrEqual('3', message:'Moins que 3 personne par chambre')]
    private ?int $personneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank( message: "Ce champ est obligatoire" )]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Chambre $chambre = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckingAt(): ?\DateTime
    {
        return $this->checkingAt;
    }

    public function setCheckingAt(\DateTime $checkingAt): static
    {
        $this->checkingAt = $checkingAt;

        return $this;
    }

    public function getCheckoutAt(): ?\DateTime
    {
        return $this->checkoutAt;
    }

    public function setCheckoutAt(\DateTime $checkoutAt): static
    {
        $this->checkoutAt = $checkoutAt;

        return $this;
    }

    public function getPrixTotal(): ?int
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(int $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPersonneNumber(): ?int
    {
        return $this->personneNumber;
    }

    public function setPersonneNumber(int $personneNumber): static
    {
        $this->personneNumber = $personneNumber;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }


}
