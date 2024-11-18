<?php

declare(strict_types=1);

namespace Gdi\Api\User\Infrastructure\Persistence\Mysql\Repository;

use Gdi\Api\User\Domain\Repository\UserWriteRepository;
use Gdi\Api\User\Domain\Entity\User;
use Gdi\Shared\Infrastructure\Persistence\Doctrine\DoctrinePostgresRegistry;

final readonly class MysqlUserWriteRepository implements UserWriteRepository
{
    public function __construct(
        private DoctrinePostgresRegistry $registry,
    ) {
    }

    public function create(User $user): void
    {
        $this->registry->persist($user);
    }
}
