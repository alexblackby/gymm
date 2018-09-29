<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileImageType extends AbstractType
{
    public function getParent()
    {
        return FileType::class;
    }
}