<?php

namespace App\Message;

class RecipeMessage
{
    public function __construct(
        public readonly string $text,
        public readonly string $url,
        public readonly string $imageUrl,
        public readonly int $telegramUserId,
    ) {
    }
}
