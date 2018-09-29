<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TrainSetsValidator extends ConstraintValidator
{
    public const SET_FIELDS = ['weight', 'reps', 'createTime'];

    public function validate($value, Constraint $constraint)
    {
        /*
         *  Проверяем, чтобы был массив, элементами которого являются массивы с полями заданными в SET_FIELDS
         */

        if (!is_array($value)) {
            $this->context->addViolation($constraint->message);
            return;
        }

        foreach ($value as $row) {
            if (!is_array($row)) {
                $this->context->addViolation($constraint->message);
                return;
            }

            $keys = array_keys($row);
            $intersectKeys = array_intersect($keys, self::SET_FIELDS);
            if (count($intersectKeys) !== count($keys) || count($intersectKeys) !== count(self::SET_FIELDS)) {
                $this->context->addViolation($constraint->message);
                return;
            }

            foreach ($row as $field) {
                if (!is_int($field)) {
                    $this->context->addViolation($constraint->message);
                    return;
                }
            }
        }
    }
}