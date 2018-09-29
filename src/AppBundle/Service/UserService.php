<?php

namespace AppBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Handler\UploadHandler;

class UserService
{
    /**
     * @var UploadHandler
     */
    private $uploadHandler;


    public function __construct(UploadHandler $uploadHandler)
    {
        $this->uploadHandler = $uploadHandler;
    }


    public function removeAvatar(UserInterface $user)
    {
        $this->uploadHandler->remove($user, 'avatar');
    }

}