<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *      attributes={"formats"={"json"}},
 *      collectionOperations={
 *          "get" = {
 *              "normalization_context" = {"groups"={"mobile:co:get"}, "swagger_definition_name"="collection"},
 *              "security"="is_granted('ROLE_USER')",
 *              "security_message"="Only authenticated users can assess this operation."
 *          },
 *      },
 *      itemOperations={
 *          "get" = {
 *              "normalization_context" = {"groups"={"mobile:io:get"}, "swagger_definition_name"="detail"}
 *          },
 *      },
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    /**
     * @ORM\ManyToOne(targetEntity=Custumer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $custumer;

    /**
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="user")
     */
    private $commands;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
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

    public function getCustumer(): ?Custumer
    {
        return $this->custumer;
    }

    public function setCustumer(?Custumer $custumer): self
    {
        $this->custumer = $custumer;

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getUser() === $this) {
                $command->setUser(null);
            }
        }

        return $this;
    }
}
