<?php

declare(strict_types=1);

namespace Gdi\Api\User\Domain\Entity;

use Gdi\Api\User\Domain\ValueObject\UserEmail;
use Gdi\Api\User\Domain\ValueObject\UserId;

final readonly class User
{
    public function __construct(
        private UserId $id,
        private UserEmail $email
    ) {
    }

    public static function create(
        UserId $id,
        UserEmail $email
    ): self {
        return new self(
            $id,
            $email
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }
}
