<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AnnonceRepository::class)
 */
class Annonce
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"annonce", "brand"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"annonce", "brand"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"annonce", "brand"})
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"annonce", "brand"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"annonce", "brand", "modele"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"annonce", "brand", "modele"})
     */
    private $km;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"annonce", "brand", "modele"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date")
     * @Groups({"annonce", "brand", "modele"})
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"annonce", "brand", "modele"})
     */
    private $fuel;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"brand", "annonce", "modele"})
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity=Garage::class, inversedBy="annonce")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"annonce", "brand", "modele"})
     */
    private $garage;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annonce")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Modele::class, inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"brand", "annonce", "modele"})
     */
    private $modele;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(int $km): self
    {
        $this->km = $km;

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

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getGarage(): ?Garage
    {
        return $this->garage;
    }

    public function setGarage(?Garage $garage): self
    {
        $this->garage = $garage;

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

    public function getModele(): ?Modele
    {
        return $this->modele;
    }

    public function setModele(?Modele $modele): self
    {
        $this->modele = $modele;

        return $this;
    }
}
