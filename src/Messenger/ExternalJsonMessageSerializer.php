<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Message\RecipeMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class ExternalJsonMessageSerializer implements SerializerInterface
{
    public function decode(array $encodedEnvelope): Envelope
    {
        $data = json_decode($encodedEnvelope['body'], true);

        $message = new RecipeMessage(
            text: $data['text'] ?? '',
            url: $data['url'] ?? '',
            imageUrl: $data['image_url'] ?? '',
            telegramUserId: $data['telegram_user_id'],
        );

        return new Envelope($message);
    }

    public function encode(Envelope $envelope): array
    {
        throw new \LogicException('This transport is receive-only.');
    }
}
