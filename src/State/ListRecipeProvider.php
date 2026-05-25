<?php
declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ListRecipeProvider implements ProviderInterface
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->recipeRepository->findAllForOwner($user->getId());
    }
}
