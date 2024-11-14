<?php

namespace Gdi\Api\Communication\Domain\Service;

use Gdi\Api\Communication\Domain\Email\EmailTemplate;
use Gdi\Api\User\Domain\ValueObject\UserEmail;

interface EmailService
{
    public function send(EmailTemplate $emailTemplate, UserEmail $userEmail): void;
}