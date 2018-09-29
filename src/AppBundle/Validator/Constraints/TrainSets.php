<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class TrainSets extends Constraint
{
    public $message = 'Формат данных: массив хэш-массивов с полями [weight:int, reps:int, createTime:int]. Все поля обязательны.';
}