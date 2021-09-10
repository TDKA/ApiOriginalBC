<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $nbPhone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $nbSiret;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     */
    private $password;

    /**
     * @EqualTo(propertyPath="password", message="Les mots de passe ne correspond pas")
     * @Groups({"user"})
     *
     */
    private  $passwordConfirm;


    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"user"})
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Garage::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user"})
     */
    private $garage;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user"})
     */
    private $annonce;

    public function __construct()
    {
        $this->garage = new ArrayCollection();
        $this->annonce = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNbPhone(): ?string
    {
        return $this->nbPhone;
    }

    public function setNbPhone(string $nbPhone): self
    {
        $this->nbPhone = $nbPhone;

        return $this;
    }

    public function getNbSiret(): ?string
    {
        return $this->nbSiret;
    }

    public function setNbSiret(string $nbSiret): self
    {
        $this->nbSiret = $nbSiret;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = "ROLE_USER";
        return array_unique($roles);

        // TODO: Implement getRoles() method.
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getUserIdentifier()
    {
        return $this->username;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    /**
     * @return Collection|Garage[]
     */
    public function getGarage(): Collection
    {
        return $this->garage;
    }

    public function addGarage(Garage $garage): self
    {
        if (!$this->garage->contains($garage)) {
            $this->garage[] = $garage;
            $garage->setUser($this);
        }

        return $this;
    }

    public function removeGarage(Garage $garage): self
    {
        if ($this->garage->removeElement($garage)) {
            // set the owning side to null (unless already changed)
            if ($garage->getUser() === $this) {
                $garage->setUser(null);
            }
        }

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
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonce->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }
}
