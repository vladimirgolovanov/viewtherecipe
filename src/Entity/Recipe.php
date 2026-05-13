<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RecipeRepository;
use App\State\RecipeStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(security: "object.getOwner() == user"),
    ],
    normalizationContext: ['groups' => ['recipe:read']],
    processor: RecipeStateProcessor::class,
)]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['recipe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['recipe:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['recipe:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['recipe:read'])]
    private ?string $source = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(nullable: true)]
    private ?int $telegram_message_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTelegramMessageId(): ?int
    {
        return $this->telegram_message_id;
    }

    public function setTelegramMessageId(?int $telegram_message_id): static
    {
        $this->telegram_message_id = $telegram_message_id;

        return $this;
    }
}
