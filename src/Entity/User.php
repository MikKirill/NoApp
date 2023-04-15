<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\User\GetCurrentController;
use App\Controller\User\RegistrationController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: UserRepository::class)]

#[
    ApiResource(
        normalizationContext: ['groups' => ['user:read']],
        denormalizationContext: ['groups' => ['user:write']],
        operations: [
            new Post(
                uriTemplate: 'user/register',
                controller: RegistrationController::class,
                denormalizationContext: ['groups' => 'createUser']
            ),
            new Get(
                uriTemplate: 'users/get-current',
                normalizationContext: ['groups' => 'image'],
                denormalizationContext: ['groups' => 'find']
            ),
            new GetCollection(),
            new Delete(),
            new Patch()
        ]
    )
]
#[ApiFilter(NumericFilter::class, properties: ['id'])]
#[ApiFilter(DateFilter::class, properties: ['created_at'])]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['id', 'created_at'], arguments: ['orderParameterName' => 'order'])]

class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['createUser', 'find'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['createUser'])]
    private ?string $password = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('read', 'write')]
    #[ApiProperty(types: ['https://schema.org/image'])]
    public ?string $cover = null;


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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        $user = new User();
        $roles = $user->getRoles();

        return array_unique($roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */

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
    public function __toString()
    {
        return $this->email;
    }
}
