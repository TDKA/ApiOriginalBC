<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GarageRepository::class)
 */
class Garage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"garage", "annonce", "user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"garage", "annonce", "user"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"garage", "annonce", "user"})
     */
    private $addresse;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"garage", "annonce", "user"})
     */
    private string $nbPhone;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"garage", "annonce", "user"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="garage", orphanRemoval=true)
     * @Groups({"garage", "user"})
     */
    private $annonce;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="garage")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"garage", "annonce"})
     */
    private $user;

    public function __construct()
    {
        $this->annonce = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): self
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getNbPhone(): ?int
    {
        return $this->nbPhone;
    }

    public function setNbPhone(int $nbPhone): self
    {
        $this->nbPhone = $nbPhone;

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

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonce(): Collection
    {
        return $this->annonce;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonce->contains($annonce)) {
            $this->annonce[] = $annonce;
            $annonce->setGarage($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonce->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getGarage() === $this) {
                $annonce->setGarage(null);
            }
        }

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
