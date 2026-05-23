<?php

namespace App\MessageHandler;

use App\Entity\Recipe;
use App\Entity\RecipeImage;
use App\Entity\User;
use App\Message\RecipeMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RecipeMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(RecipeMessage $message): void
    {
        $this->logger->info('RecipeMessage received', [
            'telegram_user_id' => $message->telegramUserId,
            'url' => $message->url,
        ]);

        try {
            $user = $this->userRepository->findOneBy(['telegram_user_id' => $message->telegramUserId]);

            if ($user === null) {
                $user = new User();
                $user->setTelegramUserId($message->telegramUserId);
                $user->setRoles([]);
                $this->entityManager->persist($user);
            }

            $recipe = new Recipe();
            $recipe->setTitle($this->extractTitle($message->text));
            $recipe->setDescription($message->text ?: null);
            $recipe->setSource($message->url ?: null);
            $recipe->setOwner($user);

            $this->entityManager->persist($recipe);

            if ($message->imageUrl !== '') {
                $image = new RecipeImage();
                $image->setUrl($message->imageUrl);
                $image->setRecipe($recipe);
                $this->entityManager->persist($image);
            }

            $this->entityManager->flush();

            $this->logger->info('RecipeMessage processed successfully', [
                'telegram_user_id' => $message->telegramUserId,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('RecipeMessage failed', [
                'telegram_user_id' => $message->telegramUserId,
                'url' => $message->url,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function extractTitle(string $text): string
    {
        if ($text === '') {
            return 'Без названия';
        }

        // Find first newline or first sentence end, whichever comes first
        $newlinePos = strpos($text, "\n");

        // Find first sentence-ending punctuation followed by space or end of string
        $sentencePos = false;
        if (preg_match('/[.!?](?:\s|$)/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            $sentencePos = $matches[0][1] + 1; // include punctuation char
        }

        if ($newlinePos !== false && ($sentencePos === false || $newlinePos <= $sentencePos)) {
            $title = substr($text, 0, $newlinePos);
        } elseif ($sentencePos !== false) {
            $title = substr($text, 0, $sentencePos);
        } else {
            $title = mb_substr($text, 0, 100);
        }

        $title = trim($title);

        if ($title === '') {
            $title = mb_substr($text, 0, 100);
        }

        return mb_substr($title, 0, 255);
    }
}
