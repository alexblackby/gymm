<?php

namespace Tests\AppBundle\Integration\Service;

use AppBundle\DTO\UserAuthData;
use AppBundle\Entity\User;
use AppBundle\Exceptions\ObjectNotFoundException;
use AppBundle\Service\Security\SecurityService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SecurityServiceTest extends KernelTestCase
{
    const TEST_USER_ID = 1;
    const TEST_USER_EMAIL = 'user@test.com';

    /** @var SecurityService */
    private $securityService;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->securityService = $container->get(SecurityService::class);
    }

    /**
     * Проверяем генерацию аутентификационных данных для АПИ (включая авторизационный JWT токен).
     */
    public function testGetAuthData()
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(self::TEST_USER_ID);
        $user->method('getEmail')->willReturn(self::TEST_USER_EMAIL);
        $user->method('getUsername')->willReturn(self::TEST_USER_EMAIL);
        $user->method('getRoles')->willReturn(array('ROLE_USER'));

        /** @var UserAuthData $authData */
        $authData = $this->securityService->getAuthData($user);

        $this->assertEquals(self::TEST_USER_ID, $authData->userId);
        $this->assertEquals(self::TEST_USER_EMAIL, $authData->userEmail);
        $this->assertNotEmpty($authData->token);

        $payloadBase64 = explode('.', $authData->token)[1];
        $payload = json_decode(base64_decode($payloadBase64));
        $this->assertEquals(self::TEST_USER_EMAIL, $payload->username);
    }

    public function testFindUserByEmail()
    {
        $user = $this->securityService->findUserByEmail(self::TEST_USER_EMAIL);
        $this->assertEquals(self::TEST_USER_EMAIL, $user->getEmail());
    }

    public function testUserNotFoundByEmail()
    {
        $this->expectException(ObjectNotFoundException::class);
        $this->securityService->findUserByEmail("not@existing.user");
    }
}