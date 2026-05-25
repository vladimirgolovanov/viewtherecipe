<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\McpTool;
use App\Dto\GetRandomRecipeInput;
use App\Repository\RecipeRepository;
use App\State\ListRecipeProvider;
use App\State\RandomRecipeProvider;
use App\State\RecipeStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    mcp: [
        'list_recipes' => new McpTool(
            description: 'List all recipes saved by the user. Returns id, title, and description for each recipe. Use this to give the user an overview of what they have saved.',
            provider: ListRecipeProvider::class,
        ),
        'get_random_recipe' => new McpTool(
            description: 'Get a random recipe for meal planning. Pass exclude_ids (array of integers) with IDs of recipes already suggested in this session to avoid repetition. Returns full recipe details: id, title, description, source URL, and images.',
            input: GetRandomRecipeInput::class,
            provider: RandomRecipeProvider::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @var Collection<int, RecipeImage>
     */
    #[ORM\OneToMany(targetEntity: RecipeImage::class, mappedBy: 'recipe', orphanRemoval: true)]
    #[Groups(['recipe:read'])]
    private Collection $images;

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

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, RecipeImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(RecipeImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setRecipe($this);
        }

        return $this;
    }

    public function removeImage(RecipeImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getRecipe() === $this) {
                $image->setRecipe(null);
            }
        }

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }
}
