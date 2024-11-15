<?php

namespace Gdi\Api\Email\Application;

use Gdi\Api\Communication\Domain\Email\WelcomeEmail;
use Gdi\Api\Communication\Domain\Service\EmailService;
use Gdi\Api\User\Domain\ValueObject\UserEmail;

final readonly class SendWelcomeEmail
{
    public function __construct(private EmailService $emailService)
    {
    }

    public function __invoke(UserEmail $userEmail): void
    {
        $welcomeEmail = new WelcomeEmail();

        $this->emailService->send($welcomeEmail, $userEmail);
    }
}