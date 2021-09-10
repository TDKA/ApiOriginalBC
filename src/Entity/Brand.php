<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BrandRepository::class)
 */
class Brand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"brand", "modele", "annonce"})
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"brand", "modele", "annonce"})
     * 
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Modele::class, mappedBy="brand", orphanRemoval=true)
     * @Groups({ "brand","modele", "annonce"})
     * 
     */
    private $modele;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="brand", orphanRemoval=true)
     *
     */
    private $annonces;

    public function __construct()
    {
        $this->modele = new ArrayCollection();
        $this->annonces = new ArrayCollection();
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

    /**
     * @return Collection|Modele[]
     */
    public function getModele(): Collection
    {
        return $this->modele;
    }

    public function addModele(Modele $modele): self
    {
        if (!$this->modele->contains($modele)) {
            $this->modele[] = $modele;
            $modele->setBrand($this);
        }

        return $this;
    }

    public function removeModele(Modele $modele): self
    {
        if ($this->modele->removeElement($modele)) {
            // set the owning side to null (unless already changed)
            if ($modele->getBrand() === $this) {
                $modele->setBrand(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setBrand($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getBrand() === $this) {
                $annonce->setBrand(null);
            }
        }

        return $this;
    }
}
