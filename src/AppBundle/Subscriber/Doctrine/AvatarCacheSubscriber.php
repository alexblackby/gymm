<?php

namespace AppBundle\Subscriber\Doctrine;


use AppBundle\Entity\User;
use AppBundle\Interfaces\HasAvatar;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Данный класс отвечает за очистку кэша картинок (thumbnails) при удалении объекта с картинкой,
 * а также при изменении картинки у объекта (например: смена аватарки пользователя)
 */
class AvatarCacheSubscriber implements EventSubscriber
{
    const PROPERTY = 'avatar';

    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;


    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }


    public function getSubscribedEvents()
    {
        return [Events::postLoad, Events::postUpdate, Events::preRemove];
    }


    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof HasAvatar) {
            $entity->_tmp_loaded_avatar_name = $entity->getAvatarName();
        }
    }


    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof HasAvatar) {
            if (isset($entity->_tmp_loaded_avatar_name)
                && $entity->_tmp_loaded_avatar_name != $entity->getAvatarName()
            ) {
                $tmp_user = new User();
                $tmp_user->setAvatarName($entity->_tmp_loaded_avatar_name);
                $path = $this->uploaderHelper->asset($tmp_user, self::PROPERTY);
                $this->cacheManager->remove($path);
            }
        }
    }


    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $path = $this->uploaderHelper->asset($entity, self::PROPERTY);
            $this->cacheManager->remove($path);
        }
    }
}
