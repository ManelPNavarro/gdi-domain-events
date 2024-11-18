<?php

namespace Gdi\Api\User\Domain\Event;

use Gdi\Shared\Domain\Event\DomainEvent;

final readonly class UserWasCreated implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $userEmail
    ) {
    }

    public static function name(): string
    {
        return 'user.created';
    }

    public function occurredOn(): int
    {
        return time();
    }
}