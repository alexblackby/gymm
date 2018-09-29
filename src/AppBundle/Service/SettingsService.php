<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Form\Settings\AvatarDeleteForm;
use AppBundle\Form\Settings\AvatarForm;
use AppBundle\Form\Settings\ChangeEmailForm;
use AppBundle\Form\Settings\ChangePasswordForm;
use AppBundle\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SettingsService
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var ConfirmEmailService
     */
    private $confirmEmailService;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var SecurityService
     */
    private $securityService;

    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        UserService $userService,
        SecurityService $securityService,
        ConfirmEmailService $confirmEmailService
    ) {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->userService = $userService;
        $this->securityService = $securityService;
        $this->confirmEmailService = $confirmEmailService;
    }


    public function processAvatarForm(FormInterface $avatarForm, Request $request)
    {
        $avatarForm->handleRequest($request);
        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Новый аватар сохранен.');
            return true;
        }
        return false;
    }


    public function processAvatarDeleteForm(FormInterface $avatarDeleteForm, Request $request, User $user)
    {
        $avatarDeleteForm->handleRequest($request);
        if ($avatarDeleteForm->isSubmitted() && $avatarDeleteForm->get('deleteAvatar')->isClicked()) {

            $this->userService->removeAvatar($user);
            $this->entityManager->flush();

            $this->session->getFlashBag()->add('success', 'Аватар удален.');
            return true;
        }
        return false;
    }

    public function processChangeEmailForm(FormInterface $changeEmailForm, Request $request, User $user)
    {
        $changeEmailForm->handleRequest($request);
        if ($changeEmailForm->isSubmitted() && $changeEmailForm->isValid()) {
            $newEmail = $changeEmailForm->getData()["email"];
            $this->confirmEmailService->sendChangeEmail($user, $newEmail);
            $this->session->getFlashBag()->add(
                'popup',
                'Теперь Вам необходимо подтвердить, что новый адрес ' . $newEmail . ' принадлежит вам. Мы отправили на него письмо, откройте его и перейдите по ссылке для подтверждения.'
            );
            return true;
        }
        return false;
    }

    public function processChangePasswordForm(FormInterface $changePasswordForm, Request $request, User $user)
    {
        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $data = $changePasswordForm->getData();
            if ($this->securityService->checkPassword($user, $data['old_password'])) {
                $user->setPlainPassword($data['new_password']);
                $this->securityService->encodePlainPassword($user);
                $this->entityManager->flush();

                $this->session->getFlashBag()->add('success', 'Ваш новый пароль сохранен.');
                return true;
            } else {
                $changePasswordForm->get('old_password')->addError(new FormError('Неверный пароль'));
                $this->session->getFlashBag()->add('danger', 'Пароль не изменен. Вы ввели неверный старый пароль');
                return false;
            }
        }
    }


    public function createChangeEmailForm(User $user)
    {
        return $this->formFactory->createNamed("change_email", ChangeEmailForm::class, ["email" => $user->getEmail()]);
    }

    public function createChangePasswordForm()
    {
        return $this->formFactory->createNamed("change_password", ChangePasswordForm::class);
    }

    public function createAvatarForm($user)
    {
        return $this->formFactory->create(AvatarForm::class, $user);
    }

    public function createAvatarDeleteForm()
    {
        return $this->formFactory->create(AvatarDeleteForm::class);
    }

}