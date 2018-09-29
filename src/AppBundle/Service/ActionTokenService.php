<?php

namespace AppBundle\Service;


use AppBundle\Entity\ActionToken;
use AppBundle\Exceptions\ObjectNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ActionTokenService
{
    const MESSAGE_TOKEN_NOT_FOUND = 'Действие не найдено. Возможно, эта ссылка уже была использована или устарела.';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $tokenString
     * @param $type
     * @return ActionToken
     * @throws ObjectNotFoundException
     */
    public function loadToken($tokenString, $type): ActionToken
    {
        $tokenParams = ActionToken::parseTokenString($tokenString);
        if (!$tokenParams) {
            throw new ObjectNotFoundException(self::MESSAGE_TOKEN_NOT_FOUND);
        }

        /** @var ActionToken $actionToken */
        $actionToken = $this->entityManager->getRepository(ActionToken::class)->findOneById($tokenParams['id']);

        if (!$actionToken || !$actionToken->validate($tokenParams['secret'], $type)) {
            throw new ObjectNotFoundException(self::MESSAGE_TOKEN_NOT_FOUND);
        }

        return $actionToken;
    }
}