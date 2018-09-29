<?php

namespace AppBundle\Validator\Constraints;

use EmailChecker\EmailChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailTrustedValidator extends ConstraintValidator
{
    /**
     * @var EmailChecker
     */
    protected $emailChecker;

    public function __construct(EmailChecker $emailChecker)
    {
        $this->emailChecker = $emailChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->context->addViolation($constraint->emailFormatMessage);
            return;
        }

        if (!$this->emailChecker->isValid($value)) {
            $this->context->addViolation($constraint->disposableServiceDetectedMessage);
        }
    }
}