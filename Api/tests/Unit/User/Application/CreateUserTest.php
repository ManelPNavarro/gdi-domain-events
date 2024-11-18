<?php

namespace Gdi\Api\Tests\Unit\User\Application;

use Gdi\Api\Crm\Domain\Dto\CrmUserDto;
use Gdi\Api\Email\Domain\Template\ConfirmationEmail;
use Gdi\Api\Email\Domain\Template\WelcomeEmail;
use Gdi\Api\Tests\Mock\Crm\Domain\Repository\InMemoryCrmWriteRepository;
use Gdi\Api\Tests\Mock\Email\Domain\Service\InMemoryEmailService;
use Gdi\Api\Tests\Mock\User\Domain\Repository\InMemoryUserProfileWriteRepository;
use Gdi\Api\Tests\Mock\User\Domain\Repository\InMemoryUserWriteRepository;
use Gdi\Api\User\Application\CreateUser;
use Gdi\Api\User\Domain\ValueObject\UserEmail;
use Gdi\Api\User\Domain\ValueObject\UserProfileName;
use PHPUnit\Framework\TestCase;

final class CreateUserTest extends TestCase
{
    private const string USER_EMAIL = 'manel@email.test';

    private InMemoryUserWriteRepository $userWriteRepository;
    private InMemoryUserProfileWriteRepository $userProfileWriteRepository;
    private InMemoryCrmWriteRepository $crmWriteRepository;
    private InMemoryEmailService $emailService;
    private CreateUser $createUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userWriteRepository = new InMemoryUserWriteRepository();
        $this->userProfileWriteRepository = new InMemoryUserProfileWriteRepository();
        $this->crmWriteRepository = new InMemoryCrmWriteRepository();
        $this->emailService = new InMemoryEmailService();

        $this->createUser = new CreateUser(
            $this->userWriteRepository,
            $this->userProfileWriteRepository,
            $this->crmWriteRepository,
            $this->emailService
        );
    }

    public function testThatUserIsCreated(): void
    {
        $userEmail = UserEmail::create(self::USER_EMAIL);

        $this->createUser->__invoke($userEmail);

        self::assertCount(1, $this->userWriteRepository->users);

        $createdUser = reset($this->userWriteRepository->users);

        self::assertEquals($userEmail, $createdUser->email());
    }

    public function testThatDefaultMainUserProfileIsCreated(): void
    {
        $userEmail = UserEmail::create(self::USER_EMAIL);

        $this->createUser->__invoke($userEmail);

        self::assertCount(1, $this->userProfileWriteRepository->userProfiles);

        $createdUser = reset($this->userWriteRepository->users);
        $createdUserProfile = $this->userProfileWriteRepository->userProfiles[$createdUser->id()->value()];

        self::assertEquals($createdUser->id(), $createdUserProfile->userId());
        self::assertEquals(UserProfileName::defaultMain(), $createdUserProfile->name());
        self::assertTrue($createdUserProfile->isMain());
    }

    public function testThatCrmUserIsCreated(): void
    {
        $userEmail = UserEmail::create(self::USER_EMAIL);

        $this->createUser->__invoke($userEmail);

        $createdUser = reset($this->userWriteRepository->users);

        $expectedCreatedCrmUser = new CrmUserDto($createdUser->id(), $userEmail);

        self::assertCount(1, $this->crmWriteRepository->crmUsers);
        self::assertEquals(
            $expectedCreatedCrmUser,
            $this->crmWriteRepository->crmUsers[$createdUser->id()->value()]
        );
    }

    public function testThatWelcomeEmailIsSent(): void
    {
        $userEmail = UserEmail::create(self::USER_EMAIL);

        $this->createUser->__invoke($userEmail);

        $sentEmailTemplate = $this->emailService->sentEmails[$userEmail->value()][0];

        self::assertInstanceOf(WelcomeEmail::class, $sentEmailTemplate);
    }

    public function tesThatConfirmationEmailIsSent(): void
    {
        $userEmail = UserEmail::create(self::USER_EMAIL);

        $this->createUser->__invoke($userEmail);

        $sentEmailTemplate = $this->emailService->sentEmails[$userEmail->value()][1];

        self::assertInstanceOf(ConfirmationEmail::class, $sentEmailTemplate);
    }
}