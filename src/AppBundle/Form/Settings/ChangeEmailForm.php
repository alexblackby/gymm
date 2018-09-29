<?php

namespace AppBundle\Form\Settings;

use AppBundle\Validator\Constraints\EmailTrusted;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotEqualTo;

class ChangeEmailForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $old_email = $builder->getData()['email'];
        $builder
            ->add(
                'email',
                EmailType::class,
                array(
                    'constraints' =>
                        array(
                            new NotEqualTo(array("value" => $old_email, "message" => "Укажите новый адрес")),
                            new EmailTrusted()
                        )
                )
            )
            ->add('save', SubmitType::class);
    }
}
