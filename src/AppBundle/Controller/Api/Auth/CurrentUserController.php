<?php

namespace AppBundle\Controller\Api\Auth;

use AppBundle\Service\Security\SecurityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CurrentUserController extends Controller
{
    /**
     * Отдает в формате JSON информацию по текущему пользователю, включая его токен для API.
     * В целях безопасности, кросс-доменнные запросы к этому экшену должны быть закрыты!
     * (исключение - запросы с localhost:8080 которые позволяют разрабатывать SPA как отдельный проект на JS)
     *
     * @Route("/auth/current-user", name="auth-current-user")
     */
    public function indexAction(SecurityService $securityService)
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse(["error" => "User not logged in"]);
        }

        $user = $this->getUser();
        $authData = $securityService->getAuthData($user);

        return new JsonResponse($authData);
    }
}
