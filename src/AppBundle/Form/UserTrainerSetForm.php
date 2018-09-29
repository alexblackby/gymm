<?php

namespace AppBundle\Form;

use AppBundle\DTO as DTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTrainerSetForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', IntegerType::class);
        $builder->add('weight', IntegerType::class);
        $builder->add('reps', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DTO\UserTrainerSet::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ));
    }
}