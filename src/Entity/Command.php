<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 * @ApiResource(
 *      attributes={
 *          "formats"={"jsonld", "json"},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      normalizationContext={
 *          "groups"="command:read",
 *          "swagger_definition_name"="read"
 *      },
 *      denormalizationContext={
 *          "groups"="command:write",
 *          "swagger_definition_name"="write"
 *      },
 *      collectionOperations={
 *          "get" = {
 *              "security"="is_granted('ROLE_USER')",
 *          },
 *          "post" = {
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context" = {"groups"={"command:write","command:read:item"}}
 *          }
 *      },
 *      itemOperations={
 *          "get" = {
 *              "security"="is_granted('ROLE_USER')",
 *              "normalization_context" = {"groups"={"command:read:item"}}
 *          },
 *          "patch" = {
 *              "normalization_context" = {"groups"={"command:update"}},
 *              "denormalization_context" = {"groups"={"command:update"}}
 *          }
 *      },
 * )
 */
class Command
{
    const STATUS_WAITING = 'En attente';
    const STATUS_VALID = 'Validé';
    const STATUS_PROGRESS = 'En cours';
    const STATUS_FINISHED = 'Terminé';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"command:read","command:read:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('En attente', 'Validé', 'En cours', 'Terminé')")
     * @Groups({"command:read","command:read:item","command:update"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commands")
     * @Groups({"command:read","command:read:item","command:write"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=Mobile::class, mappedBy="commands")
     * @Groups({"command:read","command:read:item","command:write"})
     */
    private $mobiles;

    public function __construct()
    {
        $this->mobiles = new ArrayCollection();
        $this->created_date = new \DateTime();
        $this->updated_date = new \DateTime();
        $this->status = self::STATUS_WAITING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Mobile[]
     */
    public function getMobiles(): Collection
    {
        return $this->mobiles;
    }

    public function addMobile(Mobile $mobile): self
    {
        if (!$this->mobiles->contains($mobile)) {
            $this->mobiles[] = $mobile;
            $mobile->addCommand($this);
        }

        return $this;
    }

    public function removeMobile(Mobile $mobile): self
    {
        if ($this->mobiles->removeElement($mobile)) {
            $mobile->removeCommand($this);
        }

        return $this;
    }
}
