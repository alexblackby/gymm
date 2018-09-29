<?php

namespace AppBundle\Subscriber;


use AppBundle\Event\UserSignupEvent;
use AppBundle\Service\ConfirmEmailService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Данный класс отправляет письмо со ссылкой для активации email после регистрации пользователя.
 */
class SignupConfirmationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfirmEmailService
     */
    private $confirmEmailService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SignupConfirmationSubscriber constructor.
     */
    public function __construct(ConfirmEmailService $confirmEmailService, LoggerInterface $logger)
    {
        $this->confirmEmailService = $confirmEmailService;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            UserSignupEvent::NAME => 'onUserSignup',
        );
    }

    public function onUserSignup(UserSignupEvent $event)
    {
        $user = $event->getUser();
        try {
            $this->confirmEmailService->sendConfirmEmail($user);
        } catch (\Exception $ex) {
            $this->logger->alert("Письмо с активацией не отправлено", ["email" => $user->getEmail()]);
        }
    }
}