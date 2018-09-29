<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserTrainer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTrainerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class);
        $builder->add('createTime', IntegerType::class);
        $builder->add('sets', CollectionType::class, array(
            'entry_type' => UserTrainerSetForm::class,
            'by_reference' => false,
            'allow_add' => true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserTrainer::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ));
    }
}
