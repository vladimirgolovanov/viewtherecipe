<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RandomRecipeProvider implements ProviderInterface
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $excludeIds = [];

        $mcpData = $context['mcp_data'] ?? [];
        if (isset($mcpData['exclude_ids']) && is_array($mcpData['exclude_ids'])) {
            $excludeIds = array_map('intval', $mcpData['exclude_ids']);
        }

        $recipe = $this->recipeRepository->findRandomForOwner($user->getId(), $excludeIds);

        if ($recipe === null) {
            throw new NotFoundHttpException('No more recipes available.');
        }

        return $recipe;
    }
}
