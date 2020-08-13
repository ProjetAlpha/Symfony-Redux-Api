<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="text")
     */
    private $raw_data;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_draft;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getRawData(): ?string
    {
        return $this->raw_data;
    }

    public function setRawData(string $raw_data): self
    {
        $this->raw_data = $raw_data;

        return $this;
    }

    public function getIsDraft(): ?bool
    {
        return $this->is_draft;
    }

    public function setIsDraft(?bool $is_draft): self
    {
        $this->is_draft = $is_draft;

        return $this;
    }
}
