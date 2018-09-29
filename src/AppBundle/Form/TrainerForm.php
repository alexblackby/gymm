<?php

namespace AppBundle\Form;

use AppBundle\Entity\Muscle;
use AppBundle\Entity\Trainer;
use AppBundle\Entity\TrainerCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class);
        $builder->add('description', TextType::class);
        $builder->add('category', EntityType::class, ['class' => TrainerCategory::class]);
        $builder->add('muscles', EntityType::class, ['class' => Muscle::class, 'multiple' => true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Trainer::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ));
    }
}
