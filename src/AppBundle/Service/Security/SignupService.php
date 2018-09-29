<?php

namespace AppBundle\Service\Security;


use AppBundle\DTO\UserSignupData;
use AppBundle\Entity\User;
use AppBundle\Event\UserSignupEvent;
use AppBundle\Form\UserSignupForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignupService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var LoginService
     */
    private $loginService;
    /**
     * @var SecurityService
     */
    private $securityService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginService $loginService,
        SecurityService $securityService,
        EventDispatcherInterface $dispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->loginService = $loginService;
        $this->securityService = $securityService;
        $this->dispatcher = $dispatcher;
    }


    public function createSignupForm()
    {
        $user = new User();
        return $this->formFactory->create(UserSignupForm::class, $user);
    }


    public function processSignupForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        /** @var User $user */
        $user = $form->getData();
        $this->signup($user);

        return $user;
    }


    public function signup(User $user)
    {
        $this->securityService->encodePlainPassword($user);

        $user->setHasEmailActivated(false);
        $user->setLastActivity(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $event = new UserSignupEvent($user);
        $this->dispatcher->dispatch(UserSignupEvent::NAME, $event);
    }
}