<?php

namespace Gdi\Api\User\Domain\Event;

use Gdi\Shared\Domain\Event\ApiDomainEvent;

final class UserWasCreated extends ApiDomainEvent
{
    public function __construct(
        public string $userId,
        public string $userEmail
    ) {
        parent::__construct();
    }

    public static function eventName(): string
    {
        return 'user.created';
    }

    protected static function version(): int
    {
        return 1;
    }
}