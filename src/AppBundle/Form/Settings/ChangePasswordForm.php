<?php

namespace AppBundle\Form\Settings;

use AppBundle\Form\Type\PasswordViewType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordViewType::class, ['label' => 'Старый пароль'])
            ->add('new_password', PasswordViewType::class, ['label' => 'Новый пароль'])
            ->add('save', SubmitType::class);
    }
}
