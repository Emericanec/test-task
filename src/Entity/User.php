<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="email_app_unique",
 *            columns={"email", "app_id"})
 *    }
 * )
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=255)
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255)
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Email()
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="app_id", type="integer", length=255, nullable=false)
     * @var int
     */
    protected $appId;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="parentUser")
     * @var ArrayCollection<User>
     */
    protected $childrenUser;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="childrenUser")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     * @var null|User
     */
    protected $parentUser;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     * @var null|DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable = true)
     * @var null|DateTime
     */
    protected $deletedAt;

    public function __construct()
    {
        $this->childrenUser = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getAppId(): int
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId(int $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildrenUser(): ArrayCollection
    {
        return $this->childrenUser;
    }

    public function addChildrenUser(User $childrenUser): void
    {
        $this->childrenUser[] = $childrenUser;
    }

    /**
     * @return User|null
     */
    public function getParentUser(): ?User
    {
        return $this->parentUser;
    }

    /**
     * @param User|null $parentUser
     */
    public function setParentUser(?User $parentUser): void
    {
        $this->parentUser = $parentUser;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime|null $deletedAt
     */
    public function setDeletedAt(?DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'app_id' => $this->getAppId(),
            'email' => $this->getEmail(),
            'parent_user' => $this->getParentUser() ? $this->getParentUser()->toArray() : null,
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
