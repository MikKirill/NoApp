<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity]
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]

abstract class BaseEntity
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    public int $id;

    /** Create date */
    #[ORM\Column(type: "datetime")]
    protected ?\DateTimeInterface $createdAt;

    /** Update date */
    #[ORM\Column(type: "datetime")]
    protected ?\DateTimeInterface $updatedAt;

    /** @return string */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** @return ?\DateTimeInterface */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /** @return ?\DateTimeInterface */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function dateCreate(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function dateUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
