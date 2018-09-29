<?php

namespace Tests\AppBundle\Integration\Service;

use AppBundle\Entity\User;
use AppBundle\Service\Security\SecurityService;
use AppBundle\Service\Security\SignupService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SignupServiceTest extends KernelTestCase
{
    const TEST_USER_EMAIL = 'signup.test@test.com';
    const TEST_USER_PASSWORD = 'test';

    /** @var SignupService */
    private $signupService;

    /** @var SecurityService */
    private $securityService;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->signupService = $container->get(SignupService::class);
        $this->securityService = $container->get(SecurityService::class);
    }

    /**
     * Проверяем создание пользователя в процессе регистрации
     */
    public function testSignup()
    {
        $user = new User();
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPlainPassword(self::TEST_USER_PASSWORD);
        $this->signupService->signup($user);

        $userFromDB = $this->securityService->findUserByEmail($user->getEmail());
        $this->assertNotEmpty($userFromDB);
        $this->assertEquals($user->getEmail(), $userFromDB->getEmail());
    }
}