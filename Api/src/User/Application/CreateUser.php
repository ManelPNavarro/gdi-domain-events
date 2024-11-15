<?php

namespace Gdi\Api\User\Application;

use Gdi\Api\Email\Domain\Template\ConfirmationEmail;
use Gdi\Api\Email\Domain\Template\WelcomeEmail;
use Gdi\Api\Email\Domain\Service\EmailService;
use Gdi\Api\User\Domain\Entity\User;
use Gdi\Api\User\Domain\Entity\UserProfile;
use Gdi\Api\User\Domain\Repository\UserProfileWriteRepository;
use Gdi\Api\User\Domain\Repository\UserWriteRepository;
use Gdi\Api\User\Domain\ValueObject\UserEmail;
use Gdi\Api\User\Domain\ValueObject\UserId;
use Gdi\Api\User\Domain\ValueObject\UserProfileId;
use Gdi\Shared\Domain\Exception\EmptyString;

final readonly class CreateUser
{
    public function __construct(
        private UserWriteRepository $userWriteRepository,
        private UserProfileWriteRepository $userProfileWriteRepository,
        private EmailService $emailService
    ) {
    }

    /**
     * @throws EmptyString
     */
    public function __invoke(UserEmail $userEmail): void
    {
        $userId = $this->createUserReturnId($userEmail);
        $this->createDefaultMainProfile($userId);

        $this->sendWelcomeEmail($userEmail);
        $this->sendConfirmationEmail($userEmail);
    }

    private function createUserReturnId(UserEmail $userEmail): UserId
    {
        $user = User::create(
            UserId::random(),
            $userEmail
        );

        $this->userWriteRepository->create($user);

        return $user->id();
    }

    /**
     * @throws EmptyString
     */
    private function createDefaultMainProfile(UserId $userId): void
    {
        $userProfile = UserProfile::createDefaultMain(
            UserProfileId::random(),
            $userId
        );

        $this->userProfileWriteRepository->create($userProfile);
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