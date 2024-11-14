<?php

declare(strict_types=1);

namespace Gdi\Api\User\Domain\Entity;

use Gdi\Api\User\Domain\ValueObject\UserEmail;
use Gdi\Api\User\Domain\ValueObject\UserId;
use Gdi\Api\User\Domain\ValueObject\UserName;
use Gdi\Api\User\Domain\ValueObject\UserPasswordHash;

final class User
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
}
