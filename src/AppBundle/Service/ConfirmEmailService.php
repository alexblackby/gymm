<?php

namespace AppBundle\Service;

use AppBundle\Entity\ActionToken;
use AppBundle\Service\Security\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ConfirmEmailService
{
    const TYPE_CONFIRM_EMAIL = 'confirm_email';
    const TYPE_CHANGE_EMAIL = 'change_email';


    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var ActionTokenService
     */
    private $actionTokenService;
    /**
     * @var SecurityService
     */
    private $securityService;


    public function __construct(
        ActionTokenService $actionTokenService,
        SecurityService $securityService,
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        \Twig_Environment $twig
    ) {
        $this->actionTokenService = $actionTokenService;
        $this->securityService = $securityService;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }


    /**
     * @param UserInterface $user
     * @throws \Exception
     */
    public function sendConfirmEmail(UserInterface $user)
    {
        $emailValidationToken = new ActionToken(self::TYPE_CONFIRM_EMAIL);
        $emailValidationToken->setParams(["email" => $user->getEmail()]);
        $this->entityManager->persist($emailValidationToken);
        $this->entityManager->flush();

        // todo: вынести рендеринг шаблонов в отдельный сервис, чтобы убрать из кода зависимости от Twig
        $messageBody = $this->twig->render(
            'emails/confirm_email.html.twig',
            array(
                'user' => $user,
                'token' => $emailValidationToken
            )
        );
        // todo: вынести отправку писем в отдельный service, чтобы убрать из кода зависимости от Swift
        $message = (new \Swift_Message('Confirm your email'))
            ->setFrom('noreply@gymm.online')
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }


    /**
     * @param UserInterface $user
     * @param string $newEmail
     * @throws \Exception
     */
    public function sendChangeEmail(UserInterface $user, string $newEmail)
    {
        $emailValidationToken = new ActionToken(self::TYPE_CHANGE_EMAIL);
        $emailValidationToken->setParams(["old_email" => $user->getEmail(), "new_email" => $newEmail]);

        $this->entityManager->persist($emailValidationToken);
        $this->entityManager->flush();

        // todo: вынести рендеринг шаблонов в отдельный сервис, чтобы убрать из кода зависимости от Twig
        $messageBody = $this->twig->render(
            'emails/change_email.html.twig',
            array(
                'user' => $user,
                'token' => $emailValidationToken
            )
        );
        // todo: вынести отправку писем в отдельный service, чтобы убрать из кода зависимости от Swift
        $message = (new \Swift_Message('Confirm your new email'))
            ->setFrom('noreply@gymm.online')
            ->setTo($newEmail)
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }


    public function confirmEmail($token)
    {
        $actionToken = $this->actionTokenService->loadToken($token, self::TYPE_CONFIRM_EMAIL);

        $user = $this->securityService->findUserByEmail($actionToken->getParams()['email']);
        $this->securityService->activateUserEmail($user);

        return $user;
    }

    public function changeEmail($token)
    {
        $actionToken = $this->actionTokenService->loadToken($token, self::TYPE_CHANGE_EMAIL);

        $user = $this->securityService->findUserByEmail($actionToken->getParams()['old_email']);
        $this->securityService->changeUserEmail($user, $actionToken->getParams()['new_email']);

        return $user;
    }
}