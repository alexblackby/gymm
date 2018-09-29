<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordViewType extends AbstractType
{
    public function getParent()
    {
        return PasswordType::class;
    }
}