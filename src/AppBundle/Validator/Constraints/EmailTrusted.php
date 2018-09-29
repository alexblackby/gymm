<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailTrusted extends Constraint
{
    public $emailFormatMessage = 'Проверьте правильность введенного адреса email.';
    public $disposableServiceDetectedMessage = 'Использование временной почты недопустимо. Пожалуйста, укажите основной email.';
}