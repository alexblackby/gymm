<?php

namespace AppBundle\Security;


use AppBundle\Form\LoginForm;
use AppBundle\Service\Security\SecurityService;
use AppBundle\Service\Security\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class FormLoginAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var SecurityService
     */
    private $securityService;

    public function __construct(
        FormFactoryInterface $formFactory,
        SecurityService $securityService,
        CsrfTokenManagerInterface $csrfTokenManager,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->router = $router;
        $this->securityService = $securityService;
    }

    public function supports(Request $request)
    {
        return $isLoginFormSubmitted = ($request->attributes->get('_route') == 'login' && $request->isMethod("POST"));
    }

    public function getCredentials(Request $request)
    {
        $form = $this->formFactory->createNamed('', LoginForm::class);

        $csrfToken = $request->request->get('_csrf_token');

        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $form->handleRequest($request);
        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['_username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->securityService->checkPassword($user, $credentials['_password']);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate("login"));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(
            Security::AUTHENTICATION_ERROR,
            $exception
        );

        return new RedirectResponse($this->router->generate("login"));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if (!$targetPath) {
            $targetPath = $this->router->generate("homepage");
        }
        return new RedirectResponse($targetPath);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function getLoginUrl()
    {
        return $this->router->generate("login");
    }
}