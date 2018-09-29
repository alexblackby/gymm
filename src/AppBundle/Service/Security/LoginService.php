<?php

namespace AppBundle\Service\Security;

use AppBundle\Security\FormLoginAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class LoginService
{
    /**
     * @var FormLoginAuthenticator
     */
    private $loginAuthenticator;

    /**
     * @var GuardAuthenticatorHandler
     */
    private $authenticatorHandler;


    public function __construct(
        FormLoginAuthenticator $loginAuthenticator,
        GuardAuthenticatorHandler $authenticatorHandler
    ) {
        $this->loginAuthenticator = $loginAuthenticator;
        $this->authenticatorHandler = $authenticatorHandler;
    }

    public function loginUser(UserInterface $user, Request $request)
    {
        return $this->authenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->loginAuthenticator,
            'main'
        );
    }

}