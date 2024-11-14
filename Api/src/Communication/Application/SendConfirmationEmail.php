<?php

namespace Gdi\Api\Communication\Application;

use Gdi\Api\Communication\Domain\Email\ConfirmationEmail;
use Gdi\Api\Communication\Domain\Service\EmailService;
use Gdi\Api\User\Domain\ValueObject\UserEmail;

final readonly class SendConfirmationEmail
{
    public function __construct(private EmailService $emailService)
    {
    }

    public function __invoke(UserEmail $userEmail): void
    {
        $confirmationEmail = new ConfirmationEmail();

        $this->emailService->send($confirmationEmail, $userEmail);
    }
}