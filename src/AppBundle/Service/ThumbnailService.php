<?php

namespace AppBundle\Service;

use AppBundle\Interfaces\HasAvatar;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ThumbnailService
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    public function __construct(UploaderHelper $uploaderHelper, CacheManager $imagineCacheManager)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function getWebPath(HasAvatar $entity, string $fieldName = 'avatar', string $thumbType = 'thumb')
    {
        $path = $this->uploaderHelper->asset($entity, $fieldName);
        if (!$path) {
            $className = strtolower((new \ReflectionClass($entity))->getShortName());
            $path = '/images/defaults/' . $className . '_' . $fieldName . '.png';
        }

        return $this->imagineCacheManager->getBrowserPath($path, $fieldName . '_' . $thumbType);
    }
}