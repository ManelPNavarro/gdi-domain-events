<?php

namespace Gdi\Api\User\Application;

use Gdi\Api\Crm\Domain\Dto\CrmUserDto;
use Gdi\Api\Crm\Domain\Repository\CrmWriteRepository;
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
use Gdi\Shared\Domain\ValueObject\Uuid;

final readonly class CreateUser
{
    public function __construct(
        private UserWriteRepository $userWriteRepository,
        private UserProfileWriteRepository $userProfileWriteRepository,
        private CrmWriteRepository $crmWriteRepository,
        private EmailService $emailService
    ) {
    }

    /**
     * @throws EmptyString
     */
    public function __invoke(UserEmail $email): void
    {
        $user = $this->createUser($email);
        $this->createDefaultMainProfile($user->id());

        $this->createCrmUser($user);

        $this->sendWelcomeEmail($user->email());
        $this->sendConfirmationEmail($user->email());
    }

    private function createUser(UserEmail $email): User
    {
        $user = User::create(
            UserId::create(Uuid::random()->value()),
            $email
        );

        $this->userWriteRepository->create($user);

        return $user;
    }

    /**
     * @throws EmptyString
     */
    private function createDefaultMainProfile(UserId $userId): void
    {
        $userProfile = UserProfile::createDefaultMain(
            UserProfileId::create(Uuid::random()->value()),
            $userId
        );

        $this->userProfileWriteRepository->create($userProfile);
    }

    private function createCrmUser(User $user): void
    {
        $crmUserDto = new CrmUserDto(
            $user->id(),
            $user->email()
        );

        $this->crmWriteRepository->createUser($crmUserDto);
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