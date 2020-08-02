<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=App\Repository\UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="user_id")
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_admin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmation_link;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $confirmation_link_timeout;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_link;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reset_link_timeout;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getApiToken(): string
    {
        return (string) $this->apiToken;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUserId($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getUserId() === $this) {
                $image->setUserId(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(?bool $is_admin): self
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    public function getConfirmationLink(): ?string
    {
        return $this->confirmation_link;
    }

    public function setConfirmationLink(?string $confirmation_link): self
    {
        $this->confirmation_link = $confirmation_link;

        return $this;
    }

    public function getConfirmationLinkTimeout(): ?int
    {
        return $this->confirmation_link_timeout;
    }

    public function setConfirmationLinkTimeout(?int $confirmation_link_timeout): self
    {
        $this->confirmation_link_timeout = $confirmation_link_timeout;

        return $this;
    }

    public function getResetLink(): ?string
    {
        return $this->reset_link;
    }

    public function setResetLink(?string $reset_link): self
    {
        $this->reset_link = $reset_link;

        return $this;
    }

    public function getResetLinkTimeout(): ?int
    {
        return $this->reset_link_timeout;
    }

    public function setResetLinkTimeout(?int $reset_link_timeout): self
    {
        $this->reset_link_timeout = $reset_link_timeout;

        return $this;
    }
}
