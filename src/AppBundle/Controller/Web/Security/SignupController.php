<?php

namespace AppBundle\Controller\Web\Security;

use AppBundle\DTO\UserSignupData;
use AppBundle\Service\Security\LoginService;
use AppBundle\Service\Security\SignupService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SignupController extends Controller
{
    /**
     * @Route("/signup", name="signup")
     */
    public function signupAction(Request $request, SignupService $signupService, LoginService $loginService)
    {
        $form = $signupService->createSignupForm();
        $user = $signupService->processSignupForm($form, $request);
        if ($user) {
            return $loginService->loginUser($user, $request);
        }

        return $this->render(
            'security/signup.html.twig',
            array('form' => $form->createView())
        );
    }


}