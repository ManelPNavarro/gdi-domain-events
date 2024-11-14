<?php

namespace Gdi\Api\User\Application;

use Gdi\Api\Communication\Domain\Email\ConfirmationEmail;
use Gdi\Api\Communication\Domain\Email\WelcomeEmail;
use Gdi\Api\Communication\Domain\Service\EmailService;
use Gdi\Api\User\Domain\Entity\User;
use Gdi\Api\User\Domain\Repository\UserWriteRepository;
use Gdi\Api\User\Domain\ValueObject\UserEmail;
use Gdi\Api\User\Domain\ValueObject\UserId;

final readonly class CreateUser
{
    public function __construct(
        private UserWriteRepository $userWriteRepository,
        private EmailService $emailService
    ) {
    }

    public function __invoke(UserId $userId, UserEmail $userEmail): void
    {
        $this->createUser($userId, $userEmail);
    }

    private function createUser(UserId $userId, UserEmail $userEmail): void
    {
        $user = User::create($userId, $userEmail);

        $this->userWriteRepository->create($user);
    }

    private function sendWelcomeEmail(UserEmail $userEmail): void
    {
        $welcomeEmail = new WelcomeEmail();

        $this->emailService->send($welcomeEmail, $userEmail);
    }

    private function sendConfirmationEmail(UserEmail $userEmail): void
    {
        $confirmationEmail = new ConfirmationEmail();

        $this->emailService->send($confirmationEmail, $userEmail);
    }
}