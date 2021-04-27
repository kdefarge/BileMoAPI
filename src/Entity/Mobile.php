<?php

namespace App\Entity;

use App\Repository\MobileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MobileRepository::class)
 * @ApiResource(
 *      attributes={
 *          "formats"={"json"},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      normalizationContext={
 *          "groups"={"mobile:read"},
 *          "swagger_definition_name"="read"
 *      },
 *      denormalizationContext={
 *          "groups"={"mobile:write"},
 *          "swagger_definition_name"="write"
 *      },
 *      collectionOperations={
 *          "get" = {
 *              "security"="is_granted('ROLE_USER')",
 *              "security_message"="Only authenticated users can assess this operation."
 *          },
 *          "post" = {
 *              "normalization_context" = {"groups"={"mobile:read:item"}}
 *          }
 *      },
 *      itemOperations={
 *          "get" = {
 *              "normalization_context" = {"groups"={"mobile:read:item"}},
 *              "security"="is_granted('ROLE_USER')",
 *              "security_message"="Only authenticated users can assess this operation."
 *          },
 *          "patch" = {
 *              "normalization_context" = {"groups"={"mobile:read:item"}}
 *          },
 *          "delete"
 *      },
 * )
 * @UniqueEntity(fields={"modelName"})
 */
class Mobile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mobile:read","mobile:read:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"mobile:read","mobile:read:item","mobile:write"})
     * @Assert\NotBlank()
     */
    private $modelName;

    /**
     * @ORM\Column(type="text")
     * @Groups({"mobile:read:item","mobile:write"})
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"mobile:read","mobile:read:item","mobile:write"})
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"mobile:read","mobile:read:item","mobile:write"})
     * @Assert\NotBlank()
     */
    private $stock;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    /**
     * @ORM\ManyToMany(targetEntity=Command::class, inversedBy="mobiles")
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

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(string $modelName): self
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        $this->commands->removeElement($command);

        return $this;
    }
}
