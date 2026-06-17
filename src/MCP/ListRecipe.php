<?php

declare(strict_types=1);

namespace App\MCP;

use App\Entity\User;
use App\Repository\RecipeRepository;
use Mcp\Capability\Attribute\McpTool;
use Symfony\Bundle\SecurityBundle\Security;

#[McpTool(name: 'list-recipe')]
class ListRecipe
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

        return $this->recipeRepository->findAllForOwner($user->getId());
    }
}
