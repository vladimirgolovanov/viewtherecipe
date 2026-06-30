<?php

declare(strict_types=1);

namespace App\MCP;

use App\Entity\User;
use App\Repository\RecipeRepository;
use Mcp\Capability\Attribute\McpTool;
use Symfony\Bundle\SecurityBundle\Security;

#[McpTool(name: 'get_random_recipe')]
class GetRandomRecipe
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private Security $security,
    ) {
    }

    public function __invoke()
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $randomRecipe = $this->recipeRepository->findRandomForOwner($user->getId());

        return [
            'random_recipe' => [
                'id' => $randomRecipe->getId(),
                'title' => $randomRecipe->getTitle(),
                'description' => $randomRecipe->getDescription(),
            ],
        ];
    }
}
