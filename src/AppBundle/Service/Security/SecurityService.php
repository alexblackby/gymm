<?php

namespace AppBundle\Service\Security;

use AppBundle\DTO\UserAuthData;
use AppBundle\Entity\User;
use AppBundle\Exceptions\ObjectNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityService
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }


    public function getAuthData(User $user)
    {
        $authData = new UserAuthData();
        $authData->userEmail = $user->getEmail();
        $authData->userId = $user->getId();
        $authData->token = $this->getJWT($user);

        return $authData;
    }

    public function getJWT(UserInterface $user)
    {
        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');
        $token = $jwtManager->create($user);

        return $token;
    }

    public function findUserByEmail($email)
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
        if (!$user) {
            throw new ObjectNotFoundException("Пользователь с таким e-mail не найден.");
        }
        return $user;
    }

    public function changeUserEmail(User $user, $email)
    {
        $user->setEmail($email);
        $this->activateUserEmail($user);
        $this->entityManager->flush();
    }

    public function activateUserEmail(User $user)
    {
        $user->setHasEmailActivated(true);
        $this->entityManager->flush();
    }

    public function checkPassword(UserInterface $user, string $password)
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    public function encodePlainPassword(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
    }
}