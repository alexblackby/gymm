<?php

namespace AppBundle\Controller\Web\Security;

use AppBundle\Service\ConfirmEmailService;
use AppBundle\Service\Security\LoginService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ConfirmEmailController extends Controller
{
    use TargetPathTrait;

    /**
     * @var LoginService
     */
    private $loginService;
    /**
     * @var ConfirmEmailService
     */
    private $confirmEmailService;


    public function __construct(
        ConfirmEmailService $confirmEmailService,
        LoginService $loginService
    ) {
        $this->loginService = $loginService;
        $this->confirmEmailService = $confirmEmailService;
    }


    /**
     * @Route("/confirm/email/{token}", name="confirm_email")
     */
    public function confirmEmailAction(string $token, Request $request)
    {
        $user = $this->confirmEmailService->confirmEmail($token);

        $this->addFlash('success', 'Ваш email адрес подтвержден. Спасибо!');

        return $this->loginService->loginUser($user, $request);
    }


    /**
     * @Route("/confirm/email_change/{token}", name="confirm_email_change")
     */
    public function confirmEmailChangeAction(string $token, Request $request)
    {
        $user = $this->confirmEmailService->changeEmail($token);

        $this->addFlash('success', 'Ваш email изменен на ' . $user->getEmail());
        $this->saveTargetPath($request->getSession(), "main", $this->generateUrl("settings"));

        return $this->loginService->loginUser($user, $request);
    }

}