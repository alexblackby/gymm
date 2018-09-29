<?php

namespace AppBundle\Twig;

use AppBundle\Interfaces\HasAvatar;
use AppBundle\Service\ThumbnailService;

class ImagesExtension extends \Twig_Extension
{
    /**
     * @var ThumbnailService
     */
    private $thumbnailService;

    public function __construct(ThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_Function('thumbnail', array($this, 'thumbnail')),
        );
    }

    public function thumbnail(HasAvatar $entity, string $fieldName = 'avatar', string $thumbType = 'thumb')
    {
        return $this->thumbnailService->getWebPath($entity, $fieldName, $thumbType);
    }
}