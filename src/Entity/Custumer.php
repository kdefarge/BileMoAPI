<?php

namespace App\Entity;

use App\Repository\CustumerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CustumerRepository::class)
 * @ApiResource(
 *      attributes={
 *          "formats"={"json"},
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Only admin can access this operation.",
 *      },
 *      normalizationContext={
 *          {"groups"={"admin:read"}, "swagger_definition_name"="admin-read"},
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Only admin can access this operation."
 *      },
 *      denormalizationContext={
 *          {"groups"={"admin:write"}, "swagger_definition_name"="admin-write"},
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Only admin can access this operation."
 *      },
 *      collectionOperations={
 *          "get", "post"
 *      },
 *      itemOperations={
 *          "get" = {
 *              "normalization_context" = {"groups"={"custumer:io:get"}, "swagger_definition_name"="detail"},
 *              "security"="is_granted('OWNER', object)",
 *              "security_message"="Only authenticated users can access this operation."
 *          },
 *          "delete",
 *          "put"
 *      },
 * )
 */
class Custumer implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
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
     * @ORM\Column(type="string", length=255)
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
     */
    private $name;

    /**
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @Groups({"custumer:co:get:read","custumer:io:get:read"})
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="custumer", orphanRemoval=true)
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->created_date = new \DateTime();
        $this->updated_date = new \DateTime();
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

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
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

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeInterface $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updated_date;
    }

    public function setUpdatedDate(\DateTimeInterface $updated_date): self
    {
        $this->updated_date = $updated_date;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCustumer($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCustumer() === $this) {
                $user->setCustumer(null);
            }
        }

        return $this;
    }
}
