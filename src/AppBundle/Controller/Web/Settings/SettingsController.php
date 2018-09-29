<?php

namespace AppBundle\Controller\Web\Settings;

use AppBundle\Service\SettingsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller
{
    /**
     * В целях упрощения пользовательского интерфейса, все формы настроек собраны на одной странице.
     *
     * @Route("/settings", name="settings")
     * @Security("has_role('ROLE_USER')")
     */
    public function settingsAction(Request $request, SettingsService $settingsService)
    {
        $user = $this->getUser();

        $avatarForm = $settingsService->createAvatarForm($user);
        $avatarDeleteForm = $settingsService->createAvatarDeleteForm();
        $changePasswordForm = $settingsService->createChangePasswordForm();
        $changeEmailForm = $settingsService->createChangeEmailForm($user);


        $avatarFormProcessed = $settingsService->processAvatarForm($avatarForm, $request);
        $avatarDeleteFormProcessed = $settingsService->processAvatarDeleteForm($avatarDeleteForm, $request, $user);
        $emailFormProcessed = $settingsService->processChangeEmailForm($changeEmailForm, $request, $user);
        $passwordFormProcessed = $settingsService->processChangePasswordForm($changePasswordForm, $request, $user);

        if (
            $avatarFormProcessed ||
            $avatarDeleteFormProcessed ||
            $emailFormProcessed ||
            $passwordFormProcessed
        ) {
            return $this->redirectToRoute("settings");
        }

        return $this->render(
            "settings/settings.html.twig",
            array(
                'user' => $user,
                'avatarForm' => $avatarForm->createView(),
                'avatarDeleteForm' => $avatarDeleteForm->createView(),
                'changeEmailForm' => $changeEmailForm->createView(),
                'changePasswordForm' => $changePasswordForm->createView()
            )
        );
    }


}